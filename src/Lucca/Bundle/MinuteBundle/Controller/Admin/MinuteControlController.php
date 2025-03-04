<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\Admin;

use App\Lucca\Bundle\MinuteBundle\Manager\ControlEditionManager;
use App\Lucca\Bundle\MinuteBundle\Manager\ControlManager;
use App\Lucca\Bundle\MinuteBundle\Manager\MinuteStoryManager;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Form\MinuteControlType;
use Lucca\Bundle\MinuteBundle\Utils\FolderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/minute-{minute_id}/control')]
#[IsGranted('ROLE_LUCCA')]
class MinuteControlController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MinuteStoryManager $minuteStoryManager,
        private readonly ControlManager $controlManager,
        private readonly FolderManager $folderManager,
        private readonly ControlEditionManager $controlEditionManager
    )
    {
    }

    #[Route('/new', name: 'lucca_minute_control_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(Minute $minute, Request $request): RedirectResponse|Response
    {
        $control = new Control(Control::TYPE_FOLDER);

        $form = $this->createForm(MinuteControlType::class, $control, array(
            'minute' => $minute,
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->entityManager;;

            /** Define Automatically accepted  */
            $control = $this->controlManager->defineAcceptedAutomatically($control);

            /** Add Control to Minute list */
            $control->setMinute($minute);
            /** Set automatic Minute Agent */
            $control->setAgent($minute->getAgent());

            /** If control is refused - Generate Obstacle folder */
            if ($control->getAccepted() === Control::ACCEPTED_NOK) {
                $this->folderManager->createObstacleFolder($minute, $control, Folder::TYPE_FOLDER);
                $this->addFlash('warning', 'flash.control.refused');
            }

            /** Create / update / delete editions if needed */
            $this->controlEditionManager->createEditions($control);
            $em->persist($control);

            try {
                $em->flush();
                $this->addFlash('success', 'flash.control.minute.createdSuccessfully');

                /** update status of the minute */
                $this->minuteStoryManager->manage($minute);
                $em->flush();

                return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'control-' . $control->getId()));
            }
            catch (\Exception $e){
                $this->addFlash('danger', 'flash.control.accompagnantFunctionMissing');
            }
        }

        return $this->render('@LuccaMinute/Control/new.html.twig', array(
            'control' => $control,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'user' => $minute->getAdherent()->getUser(),
            'form' => $form->createView(),
        ));
    }

    #[Route('-{id}/edit', name: 'lucca_minute_control_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(Request $request, Minute $minute, Control $control): RedirectResponse|Response
    {
        $deleteForm = $this->createDeleteForm($minute, $control);

        $editForm = $this->createForm(MinuteControlType::class, $control, array(
            'minute' => $minute, 'human' => $control->getHumansByControl()
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->entityManager;;

            /** Define Automatically accepted  */
            $control = $this->controlManager->defineAcceptedAutomatically($control);
            /** Fix a bug on 26/09/2024 thant cause 34 version to have wrong agent */
            $control->setAgent($minute->getAgent());

            /** If control is refused - Generate Obstacle folder */
            if ($control->getAccepted() === Control::ACCEPTED_NOK) {

                $this->folderManager->createObstacleFolder($minute, $control, Folder::TYPE_FOLDER);
                $this->addFlash('warning', 'flash.control.refused');
            }

            /** Create / update / delete editions if needed */
            $this->controlEditionManager->manageEditionsOnFormSubmission($control);

            $em->persist($control);
            try {
                $em->flush();
                $this->addFlash('info', 'flash.control.minute.updatedSuccessfully');
                return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'control-' . $control->getId()));
            }
            catch (\Exception $e){
                $this->addFlash('danger', 'flash.control.accompagnantFunctionMissing');
            }
        }

        return $this->render('@LuccaMinute/Control/edit.html.twig', array(
            'control' => $control,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'user' => $minute->getAdherent()->getUser(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    #[Route('-{id}/delete', name: 'lucca_minute_control_delete', methods: ['GET', 'DELETE'])]
    #[IsGranted('ROLE_LUCCA')]
    public function deleteAction(Request $request, Minute $minute, Control $control): RedirectResponse
    {
        $em = $this->entityManager;;
        $minute->removeControl($control);

        $em->remove($control);
        $em->flush();

        /** update status of the minute */
        $this->minuteStoryManager->manage($minute);
        $em->flush();

        $this->addFlash('danger', 'flash.control.minute.deletedSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }

    /**
     * Creates a form to delete a Control entity.
     *
     * @param Minute $minute
     * @param Control $control
     * @return FormInterface
     */
    private function createDeleteForm(Minute $minute, Control $control): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_minute_control_delete', array('minute_id' => $minute->getId(), 'id' => $control->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
