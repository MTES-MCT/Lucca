<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\FolderBundle\Utils\FolderManager;
use Lucca\Bundle\MinuteBundle\Entity\{Control, Updating};
use Lucca\Bundle\MinuteBundle\Form\UpdatingControlType;
use Lucca\Bundle\MinuteBundle\Manager\{ControlEditionManager, ControlManager, MinuteStoryManager};

#[Route('/updating-{updating_id}/control')]
#[IsGranted('ROLE_LUCCA')]
class UpdatingControlController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ControlManager         $controlManager,
        private readonly FolderManager          $folderManager,
        private readonly ControlEditionManager  $controlEditionManager,
        private readonly MinuteStoryManager     $minuteStoryManager
    )
    {
    }

    #[Route('/new', name: 'lucca_updating_control_new', methods: ['GET', 'POST'])]
    public function newAction(#[MapEntity(id: 'updating_id')] Updating $updating, Request $request): RedirectResponse|Response|null
    {
        $control = new Control(Control::TYPE_REFRESH);
        $control->setMinute($updating->getMinute());

        $form = $this->createForm(UpdatingControlType::class, $control, array(
            'minute' => $updating->getMinute(),
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->entityManager;;

            /** Define Automatically accepted  */
            $control = $this->controlManager->defineAcceptedAutomatically($control);

            /** Add Control to Updating list */
            $updating->addControl($control);

            /** Test if Agent is the same with minute */
            if ($form->get('sameAgent')->getData()) {
                $agentVerbalizing = $updating->getMinute()->getAgent();
            } else {
                /** If Agent is different, map field form to Entity */
                $agentVerbalizing = $form->get('agent')->getData();
            }

            $control->setAgent($agentVerbalizing);

            /** If control is refused - Generate Obstacle folder */
            if ($control->getAccepted() === Control::ACCEPTED_NOK) {
                $this->folderManager->createObstacleFolder($updating->getMinute(), $control, Folder::TYPE_FOLDER);
                $this->addFlash('warning', 'flash.control.refused');
            }

            /** Create / update / delete editions if needed */
            $this->controlEditionManager->createEditions($control);

            /** update status of the minute */
            $this->minuteStoryManager->manage($control->getMinute());

            $em->persist($control);

            try {
                $em->flush();
                $this->addFlash('success', 'flash.control.updating.createdSuccessfully');

                if ($request->request->get('saveAndContinue') !== null)
                    return $this->redirectToRoute('lucca_updating_step1', array(
                        'minute_id' => $updating->getMinute()->getId(), 'id' => $updating->getId()
                    ));

                return $this->redirectToRoute('lucca_minute_show',
                    array('id' => $updating->getMinute()->getId(), '_fragment' => 'updating-' . $updating->getId() . '#control-' . $control->getId())
                );
            } catch (\Exception $e) {
                $this->addFlash('danger', 'flash.control.accompagnantFunctionMissing');
            }
        }

        return $this->render('@LuccaMinute/Control/new.html.twig', array(
            'control' => $control,
            'updating' => $updating,
            'minute' => $updating->getMinute(),
            'adherent' => $updating->getMinute()->getAdherent(),
            'user' => $updating->getMinute()->getAdherent()->getUser(),
            'form' => $form->createView(),
        ));
    }

    #[Route('-{id}/edit', name: 'lucca_updating_control_edit', options: ['utf8' => true], methods: ['GET', 'POST'])]
    public function editAction(Request $request, #[MapEntity(id: 'updating_id')] Updating $updating, #[MapEntity(id: 'id')] Control $control): RedirectResponse|Response
    {
        $em = $this->entityManager;;
        $editForm = $this->createForm(UpdatingControlType::class, $control, array(
            'minute' => $updating->getMinute(), 'human' => $control->getHumansByControl()
        ));

        /** Init unmapped Agent form with data */
        if ($control->getAgent())
            $editForm->get('agent')->setData($control->getAgent());

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            /** Define Automatically accepted  */
            $control = $this->controlManager->defineAcceptedAutomatically($control);

            /** Test if Agent is the same with minute */
            if ($editForm->get('sameAgent')->getData()) {
                $agentVerbalizing = $updating->getMinute()->getAgent();
            } else {
                /** If Agent is different, map field form to Entity */
                $agentVerbalizing = $editForm->get('agent')->getData();
            }

            $control->setAgent($agentVerbalizing);

            /** If control is refused - Generate Obstacle folder */
            if ($control->getAccepted() === Control::ACCEPTED_NOK) {
                $this->folderManager->createObstacleFolder($updating->getMinute(), $control, Folder::TYPE_FOLDER);
                $this->addFlash('warning', 'flash.control.refused');
            }

            /** Create / update / delete editions if needed */
            $this->controlEditionManager->manageEditionsOnFormSubmission($control);

            $em->persist($control);

            try {
                $em->flush();

                $this->addFlash('info', 'flash.control.updating.updatedSuccessfully');

                if ($request->request->get('saveAndContinue') !== null)
                    return $this->redirectToRoute('lucca_updating_step1', array(
                        'minute_id' => $updating->getMinute()->getId(), 'id' => $updating->getId()
                    ));

                return $this->redirectToRoute('lucca_minute_show', array('id' => $updating->getMinute()->getId(), '_fragment' => 'updating-' . $updating->getId() . '_control-' . $control->getId()));
            } catch (\Exception $e) {
                $this->addFlash('danger', 'flash.control.accompagnantFunctionMissing');
            }
        }

        return $this->render('@LuccaMinute/Control/edit.html.twig', array(
            'control' => $control,
            'updating' => $updating,
            'minute' => $updating->getMinute(),
            'adherent' => $updating->getMinute()->getAdherent(),
            'user' => $updating->getMinute()->getAdherent()->getUser(),
            'edit_form' => $editForm->createView(),
        ));
    }

    #[Route('-{id}/delete', name: 'lucca_updating_control_delete', methods: ['GET', 'DELETE'])]
    public function deleteAction(Request $request, #[MapEntity(id: 'updating_id')] Updating $updating, #[MapEntity(id: 'id')] Control $control): RedirectResponse
    {
        $em = $this->entityManager;;
        $updating->removeControl($control);

        $em->remove($control);
        $em->flush();

        $this->minuteStoryManager->manage($control->getMinute());
        $em->flush();

        $this->addFlash('danger', 'flash.control.updating.deletedSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $updating->getMinute()->getId()));
    }
}
