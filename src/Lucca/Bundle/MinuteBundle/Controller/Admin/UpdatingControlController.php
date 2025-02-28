<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Admin;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Updating;
use Doctrine\ORM\ORMException;
use Lucca\MinuteBundle\Form\UpdatingControlType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdatingControlController
 *
 * @Route("/updating-{updating_id}/control")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("updating", class="LuccaMinuteBundle:Updating", options={"id" = "updating_id"})
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class UpdatingControlController extends Controller
{
    /**
     * Creates a new Control entity.
     *
     * @Route("/new", name="lucca_updating_control_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Updating $updating
     * @param Request $request
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    public function newAction(Updating $updating, Request $request)
    {
        $control = new Control(Control::TYPE_REFRESH);
        $control->setMinute($updating->getMinute());

        $form = $this->createForm(UpdatingControlType::class, $control, array(
            'minute' => $updating->getMinute(),
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            /** Define Automatically accepted  */
            $control = $this->get('lucca.manager.control')->defineAcceptedAutomatically($control);

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
                $this->get('lucca.manager.folder')->createObstacleFolder($updating->getMinute(), $control, Folder::TYPE_FOLDER);
                $this->addFlash('warning', 'flash.control.refused');
            }

            /** Create / update / delete editions if needed */
            $this->get('lucca.manager.control_edition')->createEditions($control);

            /** update status of the minute */
            $this->get('lucca.manager.minute_story')->manage($control->getMinute());
            
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
            }
            catch (\Exception $e){
                $this->addFlash('danger', 'flash.control.accompagnantFunctionMissing');
            }
        }

        return $this->render('LuccaMinuteBundle:Control:new.html.twig', array(
            'control' => $control,
            'updating' => $updating,
            'minute' => $updating->getMinute(),
            'adherent' => $updating->getMinute()->getAdherent(),
            'user' => $updating->getMinute()->getAdherent()->getUser(),
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Control entity.
     *
     * @Route("-{id}/edit", name="lucca_updating_control_edit", methods={"GET", "POST"}, options = { "utf8": true })
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Updating $updating
     * @param Control $control
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Updating $updating, Control $control)
    {
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(UpdatingControlType::class, $control, array(
            'minute' => $updating->getMinute(), 'human' => $control->getHumansByControl()
        ));

        /** Init unmapped Agent form with data */
        if ($control->getAgent())
            $editForm->get('agent')->setData($control->getAgent());

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            /** Define Automatically accepted  */
            $control = $this->get('lucca.manager.control')->defineAcceptedAutomatically($control);

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
                $this->get('lucca.manager.folder')->createObstacleFolder($updating->getMinute(), $control, Folder::TYPE_FOLDER);
                $this->addFlash('warning', 'flash.control.refused');
            }

            /** Create / update / delete editions if needed */
            $this->get('lucca.manager.control_edition')->manageEditionsOnFormSubmission($control);

            $em->persist($control);

            try {
                $em->flush();

                $this->addFlash('info', 'flash.control.updating.updatedSuccessfully');

                if ($request->request->get('saveAndContinue') !== null)
                    return $this->redirectToRoute('lucca_updating_step1', array(
                        'minute_id' => $updating->getMinute()->getId(), 'id' => $updating->getId()
                    ));

                return $this->redirectToRoute('lucca_minute_show', array('id' => $updating->getMinute()->getId(), '_fragment' => 'updating-' . $updating->getId() . '_control-' . $control->getId() ));
            }
            catch (\Exception $e){
                $this->addFlash('danger', 'flash.control.accompagnantFunctionMissing');
            }
        }

        return $this->render('LuccaMinuteBundle:Control:edit.html.twig', array(
            'control' => $control,
            'updating' => $updating,
            'minute' => $updating->getMinute(),
            'adherent' => $updating->getMinute()->getAdherent(),
            'user' => $updating->getMinute()->getAdherent()->getUser(),
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Control entity.
     *
     * @Route("-{id}/delete", name="lucca_updating_control_delete", methods={"GET", "DELETE"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Updating $updating
     * @param Control $control
     * @return RedirectResponse
     * @throws ORMException
     */
    public function deleteAction(Request $request, Updating $updating, Control $control)
    {
        $em = $this->getDoctrine()->getManager();
        $updating->removeControl($control);

        $em->remove($control);
        $em->flush();

        $this->get('lucca.manager.minute_story')->manage($control->getMinute());
        $em->flush();

        $this->addFlash('danger', 'flash.control.updating.deletedSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $updating->getMinute()->getId()));
    }
}
