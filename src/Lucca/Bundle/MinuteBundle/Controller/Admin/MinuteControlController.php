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
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Doctrine\ORM\ORMException;
use Lucca\MinuteBundle\Form\MinuteControlType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MinuteControlController
 *
 * @Route("/minute-{minute_id}/control")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class MinuteControlController extends Controller
{
    /**
     * Creates a new Control entity.
     *
     * @Route("/new", name="lucca_minute_control_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @param Request $request
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    public function newAction(Minute $minute, Request $request)
    {
        $control = new Control(Control::TYPE_FOLDER);

        $form = $this->createForm(MinuteControlType::class, $control, array(
            'minute' => $minute,
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /** Define Automatically accepted  */
            $control = $this->get('lucca.manager.control')->defineAcceptedAutomatically($control);

            /** Add Control to Minute list */
            $control->setMinute($minute);
            /** Set automatic Minute Agent */
            $control->setAgent($minute->getAgent());

            /** If control is refused - Generate Obstacle folder */
            if ($control->getAccepted() === Control::ACCEPTED_NOK) {
                $this->get('lucca.manager.folder')->createObstacleFolder($minute, $control, Folder::TYPE_FOLDER);
                $this->addFlash('warning', 'flash.control.refused');
            }

            /** Create / update / delete editions if needed */
            $this->get('lucca.manager.control_edition')->createEditions($control);
            $em->persist($control);

            try {
                $em->flush();
                $this->addFlash('success', 'flash.control.minute.createdSuccessfully');

                /** update status of the minute */
                $this->get('lucca.manager.minute_story')->manage($minute);
                $em->flush();

                return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'control-' . $control->getId()));
            }
            catch (\Exception $e){
                $this->addFlash('danger', 'flash.control.accompagnantFunctionMissing');
            }
        }

        return $this->render('LuccaMinuteBundle:Control:new.html.twig', array(
            'control' => $control,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'user' => $minute->getAdherent()->getUser(),
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Control entity.
     *
     * @Route("-{id}/edit", name="lucca_minute_control_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Control $control
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Minute $minute, Control $control)
    {
        $deleteForm = $this->createDeleteForm($minute, $control);

        $editForm = $this->createForm(MinuteControlType::class, $control, array(
            'minute' => $minute, 'human' => $control->getHumansByControl()
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /** Define Automatically accepted  */
            $control = $this->get('lucca.manager.control')->defineAcceptedAutomatically($control);
            /** Fix a bug on 26/09/2024 thant cause 34 version to have wrong agent */
            $control->setAgent($minute->getAgent());

            /** If control is refused - Generate Obstacle folder */
            if ($control->getAccepted() === Control::ACCEPTED_NOK) {

                $this->get('lucca.manager.folder')->createObstacleFolder($minute, $control, Folder::TYPE_FOLDER);
                $this->addFlash('warning', 'flash.control.refused');
            }

            /** Create / update / delete editions if needed */
            $this->get('lucca.manager.control_edition')->manageEditionsOnFormSubmission($control);

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

        return $this->render('LuccaMinuteBundle:Control:edit.html.twig', array(
            'control' => $control,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'user' => $minute->getAdherent()->getUser(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Control entity.
     *
     * @Route("-{id}/delete", name="lucca_minute_control_delete", methods={"GET", "DELETE"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Control $control
     * @return RedirectResponse
     * @throws ORMException
     */
    public function deleteAction(Request $request, Minute $minute, Control $control)
    {
        $em = $this->getDoctrine()->getManager();
        $minute->removeControl($control);

        $em->remove($control);
        $em->flush();

        /** update status of the minute */
        $this->get('lucca.manager.minute_story')->manage($minute);
        $em->flush();

        $this->addFlash('danger', 'flash.control.minute.deletedSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }

    /**
     * Creates a form to delete a Control entity.
     *
     * @param Minute $minute
     * @param Control $control
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Minute $minute, Control $control)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_minute_control_delete', array('minute_id' => $minute->getId(), 'id' => $control->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
