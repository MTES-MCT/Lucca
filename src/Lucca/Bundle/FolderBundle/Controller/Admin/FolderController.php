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
use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Doctrine\ORM\ORMException;
use Lucca\MinuteBundle\Form\FolderStep1Type;
use Lucca\MinuteBundle\Form\FolderStep2Type;
use Lucca\MinuteBundle\Form\FolderStep3Type;
use Lucca\MinuteBundle\Form\FolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FolderController
 *
 * @Route("/minute-{minute_id}/folder")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class FolderController extends Controller
{
    /**
     * Creates a new Folder entity.
     *
     * @Route("/new", name="lucca_folder_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @param Request $request
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    public function newAction(Minute $minute, Request $request)
    {
        $folder = new Folder();

        $folder->setMinute($minute);
        $folder->setType(Folder::TYPE_FOLDER);

        /** Add all default element to checklist */
        $folder = $this->get('lucca.manager.folder')->addChecklistToFolder($folder);

        /** Create form */
        $form = $this->createForm(FolderType::class, $folder, array(
            'minute' => $minute,
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** If there is no control linked to the folder do nothing and display an error message */
            if (!$folder->getControl()){
                $this->addFlash('danger', 'flash.control.needed');
            } else {
                $em = $this->getDoctrine()->getManager();

                /** Create folder num */
                $folder->setNum($this->get('lucca.generator.folder_num')->generate($folder));

                /** Create / update / delete editions if needed */
                $this->get('lucca.manager.folder_edition')->manageEditionsOnFormSubmission($folder);

                /** Config auto to Obstacle folder */
                if ($folder->getNature() === Folder::NATURE_OBSTACLE)
                    $this->get('lucca.manager.folder')->configureObstacleFolder($folder);

                /** Persist folder */
                $em->persist($folder);
                $em->flush();

                /** update status of the minute */
                $this->get('lucca.manager.minute_story')->manage($minute);
                $em->flush();

                $this->addFlash('success', 'flash.folder.createdSuccessfully');

                if ($request->request->get('saveAndContinue') !== null)
                    return $this->redirectToRoute('lucca_folder_step1', array(
                        'minute_id' => $minute->getId(), 'id' => $folder->getId()
                    ));

                return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'folder-' . $folder->getId()));
            }
        }

        return $this->render('LuccaMinuteBundle:Folder:new.html.twig', array(
            'folder' => $folder,
            'minute' => $minute,
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit or create Step 1 - Folder
     *
     * @Route("-{id}/step-1", name="lucca_folder_step1", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response|null
     */
    public function step1Action(Request $request, Minute $minute, Folder $folder)
    {
        if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
            $this->addFlash('warning', 'flash.folder.step1CannotBeEdited');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('LuccaMinuteBundle:Tag')->findValuesByCategory(Tag::CATEGORY_NATURE);

        $form = $this->createForm(FolderStep1Type::class, $folder, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Call service to clean all html of this step from useless fonts */
            $folder->setAscertainment($this->get('lucca.utils.html_cleaner')->removeAllFonts($folder->getAscertainment()));

            /** Clean html from useless fonts */
            $em->persist($folder);
            $em->flush();

            $this->addFlash('success', 'flash.folder.step1Saved');

            if ($request->request->get('saveAndContinue') !== null)
                return $this->redirectToRoute('lucca_folder_step2', array(
                    'minute_id' => $minute->getId(), 'id' => $folder->getId()
                ));

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('LuccaMinuteBundle:Folder:step1.html.twig', array(
            'folder' => $folder,
            'minute' => $minute,
            'tags' => $tags,
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit or create Step 2 - Folder
     *
     * @Route("-{id}/step-2", name="lucca_folder_step2", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response|null
     */
    public function step2Action(Request $request, Minute $minute, Folder $folder)
    {
        if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
            $this->addFlash('warning', 'flash.folder.step2CannotBeEdited');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('LuccaMinuteBundle:Tag')->findValuesByCategory(Tag::CATEGORY_TOWN);

        $form = $this->createForm(FolderStep2Type::class, $folder, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Call service to clean all html of this step from useless fonts */
            $folder->setViolation($this->get('lucca.utils.html_cleaner')->removeAllFonts($folder->getViolation()));
            $folder->setDetails($this->get('lucca.utils.html_cleaner')->removeAllFonts($folder->getDetails()));

            $em->persist($folder);
            $em->flush();

            $this->addFlash('success', 'flash.folder.step2Saved');

            if ($request->request->get('saveAndContinue') !== null)
                return $this->redirectToRoute('lucca_folder_step3', array(
                    'minute_id' => $minute->getId(), 'id' => $folder->getId()
                ));

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('LuccaMinuteBundle:Folder:step2.html.twig', array(
            'folder' => $folder,
            'minute' => $minute,
            'tags' => $tags,
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit or create Step 3 - Folder
     *
     * @Route("-{id}/step-3", name="lucca_folder_step3", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response|null
     */
    public function step3Action(Request $request, Minute $minute, Folder $folder)
    {
        if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
            $this->addFlash('warning', 'flash.folder.step3CannotBeEdited');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();

        $natinfs = $em->getRepository('LuccaMinuteBundle:Natinf')->findAllByStatus(true);
        $natinfsFiltered = $em->getRepository('LuccaMinuteBundle:Natinf')->findNatinfsByFolder($folder);

        $form = $this->createForm(FolderStep3Type::class, $folder, array('natinfsFiltered' => $natinfsFiltered));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($folder);
            $em->flush();

            $this->addFlash('success', 'flash.folder.step3Saved');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('LuccaMinuteBundle:Folder:step3.html.twig', array(
            'folder' => $folder,
            'minute' => $minute,
            'natinfs' => $natinfs,
            'natinfsFiltered' => $natinfsFiltered,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Folder entity.
     *
     * @Route("-{id}/edit", name="lucca_folder_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    public function editAction(Request $request, Minute $minute, Folder $folder)
    {
        $editForm = $this->createForm(FolderType::class, $folder, array(
            'minute' => $minute,
        ));

        $editForm->remove('control');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /** Create / update / delete editions if needed */
            $this->get('lucca.manager.folder_edition')->manageEditionsOnFormSubmission($folder);

            /** Config auto to Obstacle folder */
            if ($folder->getNature() === Folder::NATURE_OBSTACLE)
                $this->get('lucca.manager.folder')->configureObstacleFolder($folder);

            /** update status of the minute */
            $this->get('lucca.manager.minute_story')->manage($minute);

            $em->persist($folder);
            $em->flush();

            $this->addFlash('info', 'flash.folder.updatedSuccessfully');

            if ($request->request->get('saveAndContinue') !== null)
                return $this->redirectToRoute('lucca_folder_step1', array(
                    'minute_id' => $minute->getId(), 'id' => $folder->getId()
                ));

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'folder-' . $folder->getId()));
        }

        return $this->render('@LuccaMinute/Folder/edit.html.twig', array(
            'folder' => $folder,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'user' => $minute->getAdherent()->getUser(),
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Folder entity.
     *
     * @Route("-{id}/delete", name="lucca_folder_delete", methods={"GET", "DELETE"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     * @throws ORMException
     */
    public function deleteAction(Request $request, Minute $minute, Folder $folder)
    {
        $em = $this->getDoctrine()->getManager();

        $folder->getControl()->setIsFenced(false);
        $folder->getControl()->setFolder(null);
        $em->persist($folder->getControl());

        $em->remove($folder);
        $em->flush();

        /** update status of the minute */
        $this->get('lucca.manager.minute_story')->manage($minute);
        $em->flush();

        $this->addFlash('danger', 'flash.folder.deletedSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }

    /**
     * Creates a form to delete a Folder entity.
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Minute $minute, Folder $folder)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_folder_delete', array('minute_id' => $minute->getId(), 'id' => $folder->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Close a Folder entity.
     *
     * @Route("-{id}/close", name="lucca_folder_fence")
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     * @throws \Exception
     */
    public function fenceAction(Minute $minute, Folder $folder)
    {
        $em = $this->getDoctrine()->getManager();

        $this->get('lucca.manager.folder')->closeFolder($folder);

        $em->flush();

        /** update status of the minute */
        $this->get('lucca.manager.minute_story')->manage($minute);
        $em->flush();

        $this->addFlash('success', 'flash.folder.closeSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }

    /**
     * Open a Folder entity.
     *
     * @Route("-{id}/open", name="lucca_folder_open")
     * @Security("has_role('ROLE_FOLDER_OPEN')")
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     */
    public function openAction(Minute $minute, Folder $folder)
    {
        $em = $this->getDoctrine()->getManager();

        $folder->setDateClosure(null);
        $folder->getControl()->setIsFenced(false);

        $em->persist($folder);
        $em->persist($folder->getControl());
        $em->flush();

        $this->addFlash('info', 'flash.folder.openSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'folder-' . $folder->getId()));
    }

    /**
     * Open a Folder entity.
     *
     * @Route("-{id}/reread", name="lucca_folder_reread")
     * @Route("-{id}/unreread", name="lucca_folder_unreread")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     */
    public function rereadAction(Minute $minute, Folder $folder)
    {
        $em = $this->getDoctrine()->getManager();

        if ($folder->getIsReReaded()) {
            $folder->setIsReReaded(false);
            $this->addFlash('info', 'flash.folder.markAsReReaded');
        } else {
            $folder->setIsReReaded(true);
            $this->addFlash('info', 'flash.folder.markAsUnReReaded');
        }

        $em->persist($folder);
        $em->flush();

        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'folder-' . $folder->getId()));
    }
}
