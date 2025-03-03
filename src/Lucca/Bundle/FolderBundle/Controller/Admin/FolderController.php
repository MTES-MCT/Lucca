<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\FolderBundle\Entity\Tag;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Doctrine\ORM\ORMException;
use Lucca\Bundle\FolderBundle\Form\FolderStep1Type;
use Lucca\Bundle\FolderBundle\Form\FolderStep2Type;
use Lucca\Bundle\FolderBundle\Form\FolderStep3Type;
use Lucca\Bundle\FolderBundle\Form\FolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class FolderController
 *
 * @package Lucca\Bundle\FolderBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
#[IsGranted('ROLE_LUCCA')]
#[Route('/minute-{minute_id}/folder')]
class FolderController extends AbstractController
{
    /**
     * Creates a new Folder entity.
     *
     * @param Minute $minute
     * @param Request $request
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    #[Route('/new', name: 'lucca_folder_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(Minute $minute, Request $request): RedirectResponse|Response|null
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

        return $this->render('@LuccaFolder/Folder/new.html.twig', array(
            'folder' => $folder,
            'minute' => $minute,
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit or create Step 1 - Folder
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response|null
     */
    #[Route('-{id}/step-1', name: 'lucca_folder_step1', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function step1Action(Request $request, Minute $minute, Folder $folder): RedirectResponse|Response|null
    {
        if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
            $this->addFlash('warning', 'flash.folder.step1CannotBeEdited');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('LuccaFolderBundle:Tag')->findValuesByCategory(Tag::CATEGORY_NATURE);

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

        return $this->render('@LuccaFolder/Folder/step1.html.twig', array(
            'folder' => $folder,
            'minute' => $minute,
            'tags' => $tags,
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit or create Step 2 - Folder
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response|null
     */
    #[Route('-{id}/step-2', name: 'lucca_folder_step2', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function step2Action(Request $request, Minute $minute, Folder $folder): RedirectResponse|Response|null
    {
        if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
            $this->addFlash('warning', 'flash.folder.step2CannotBeEdited');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('LuccaFolderBundle:Tag')->findValuesByCategory(Tag::CATEGORY_TOWN);

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

        return $this->render('@LuccaFolder/Folder/step2.html.twig', array(
            'folder' => $folder,
            'minute' => $minute,
            'tags' => $tags,
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit or create Step 3 - Folder
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response|null
     */
    #[Route('-{id}/step-3', name: 'lucca_folder_step3', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function step3Action(Request $request, Minute $minute, Folder $folder): RedirectResponse|Response|null
    {
        if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
            $this->addFlash('warning', 'flash.folder.step3CannotBeEdited');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();

        $natinfs = $em->getRepository('LuccaFolderBundle:Natinf')->findAllByStatus(true);
        $natinfsFiltered = $em->getRepository('LuccaFolderBundle:Natinf')->findNatinfsByFolder($folder);

        $form = $this->createForm(FolderStep3Type::class, $folder, array('natinfsFiltered' => $natinfsFiltered));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($folder);
            $em->flush();

            $this->addFlash('success', 'flash.folder.step3Saved');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('@LuccaFolder/Folder/step3.html.twig', array(
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
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    #[Route('-{id}/edit', name: 'lucca_folder_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(Request $request, Minute $minute, Folder $folder): RedirectResponse|Response|null
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
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     * @throws ORMException
     */
    #[Route('-{id}/delete', name: 'lucca_folder_delete', methods: ['GET', 'DELETE'])]
    #[IsGranted('ROLE_LUCCA')]
    public function deleteAction(Request $request, Minute $minute, Folder $folder): RedirectResponse
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
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     * @throws \Exception
     */
    #[Route('-{id}/close', name: 'lucca_folder_fence')]
    #[IsGranted('ROLE_LUCCA')]
    public function fenceAction(Minute $minute, Folder $folder): RedirectResponse
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
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     */
    #[Route('-{id}/open', name: 'lucca_folder_open')]
    #[IsGranted('ROLE_FOLDER_OPEN')]
    public function openAction(Minute $minute, Folder $folder): RedirectResponse
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
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     */
    #[Route('-{id}/reread', name: 'lucca_folder_reread')]
    #[Route('-{id}/unreread', name: 'lucca_folder_unreread')]
    #[IsGranted('ROLE_ADMIN')]
    public function rereadAction(Minute $minute, Folder $folder): RedirectResponse
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
