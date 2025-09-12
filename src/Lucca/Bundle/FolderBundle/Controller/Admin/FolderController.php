<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\{ElementChecked, Folder, Natinf, Tag};
use Lucca\Bundle\FolderBundle\Form\{FolderStep1Type, FolderStep2Type, FolderStep3Type, FolderType};
use Lucca\Bundle\FolderBundle\Generator\NumFolderGenerator;
use Lucca\Bundle\FolderBundle\Utils\{FolderEditionManager, FolderManager};
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Manager\MinuteStoryManager;
use Lucca\Bundle\MinuteBundle\Utils\HtmlCleaner;

#[IsGranted('ROLE_LUCCA')]
#[Route(path: '/minute-{minute_id}/folder')]
class FolderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FolderManager          $folderManager,
        private readonly NumFolderGenerator     $numFolderGenerator,
        private readonly FolderEditionManager   $folderEditionManager,
        private readonly MinuteStoryManager     $minuteStoryManager,
        private readonly HtmlCleaner            $htmlCleaner,
    )
    {
    }

    /**
     * Creates a new Folder entity.
     *
     * @throws ORMException
     */
    #[Route(path: '/new', name: 'lucca_folder_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Request $request
    ): Response
    {
        $availableControls = $minute->getControlsForFolder();
        if(count($availableControls) === 0) {
            $this->addFlash('warning', 'flash.control.needed');
            return $this->redirectToRoute('lucca_minute_show', [
                'id' => $minute->getId()
            ]);
        }

        $folder = new Folder();

        $folder->setMinute($minute);
        $folder->setType(Folder::TYPE_FOLDER);

        /** Add all default element to checklist */
        $folder = $this->folderManager->addChecklistToFolder($folder);

        /** Create form */
        $form = $this->createForm(FolderType::class, $folder, [
            'minute' => $minute,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** If there is no control linked to the folder do nothing and display an error message */
            if (!$folder->getControl()){
                $this->addFlash('danger', 'flash.control.needed');
            } else {
                /** Create folder num */
                $folder->setNum($this->numFolderGenerator->generate($folder));

                /** Create / update / delete editions if needed */
                $this->folderEditionManager->manageEditionsOnFormSubmission($folder);

                /** Config auto to Obstacle folder */
                if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
                    $this->folderManager->configureObstacleFolder($folder);
                }

                /** Persist folder */
                $this->em->persist($folder);
                $this->em->flush();

                /** update status of the minute */
                $this->minuteStoryManager->manage($minute);
                $this->em->flush();

                $this->addFlash('success', 'flash.folder.createdSuccessfully');

                if ($request->request->get('saveAndContinue') !== null) {
                    return $this->redirectToRoute('lucca_folder_step1', [
                        'minute_id' => $minute->getId(),
                        'id' => $folder->getId(),
                    ]);
                }

                return $this->redirectToRoute('lucca_minute_show', [
                    'id' => $minute->getId(),
                    '_fragment' => 'folder-' . $folder->getId(),
                ]);
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
     */
    #[Route(path: '-{id}/step-1', name: 'lucca_folder_step1', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function step1Action(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder,
        Request $request,
    ): Response
    {
        if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
            $this->addFlash('warning', 'flash.folder.step1CannotBeEdited');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        $tags = $this->em->getRepository(Tag::class)->findValuesByCategory(Tag::CATEGORY_NATURE);

        $form = $this->createForm(FolderStep1Type::class, $folder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Call service to clean all html of this step from useless fonts */
            $folder->setAscertainment($this->htmlCleaner->removeAllFonts($folder->getAscertainment()));

            /** Clean html from useless fonts */
            $this->em->persist($folder);
            $this->em->flush();

            $this->addFlash('success', 'flash.folder.step1Saved');

            if ($request->request->get('saveAndContinue') !== null) {
                return $this->redirectToRoute('lucca_folder_step2', [
                    'minute_id' => $minute->getId(),
                    'id' => $folder->getId(),
                ]);
            }

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return $this->render('@LuccaFolder/Folder/step1.html.twig', [
            'folder' => $folder,
            'minute' => $minute,
            'tags' => $tags,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit or create Step 2 - Folder
     */
    #[Route(path: '-{id}/step-2', name: 'lucca_folder_step2', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function step2Action(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Request $request,
        Folder $folder
    ): Response
    {
        if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
            $this->addFlash('warning', 'flash.folder.step2CannotBeEdited');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        $tags = $this->em->getRepository(Tag::class)->findValuesByCategory(Tag::CATEGORY_TOWN);

        $form = $this->createForm(FolderStep2Type::class, $folder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Call service to clean all html of this step from useless fonts */
            $folder->setViolation($this->htmlCleaner->removeAllFonts($folder->getViolation()));
            $folder->setDetails($this->htmlCleaner->removeAllFonts($folder->getDetails()));

            $this->em->persist($folder);
            $this->em->flush();

            $this->addFlash('success', 'flash.folder.step2Saved');

            if ($request->request->get('saveAndContinue') !== null) {
                return $this->redirectToRoute('lucca_folder_step3', [
                    'minute_id' => $minute->getId(),
                    'id' => $folder->getId(),
                ]);
            }

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return $this->render('@LuccaFolder/Folder/step2.html.twig', [
            'folder' => $folder,
            'minute' => $minute,
            'tags' => $tags,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit or create Step 3 - Folder
     */
    #[Route(path: '-{id}/step-3', name: 'lucca_folder_step3', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function step3Action(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Request $request,
        Folder $folder
    ): Response
    {
        if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
            $this->addFlash('warning', 'flash.folder.step3CannotBeEdited');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        $natinfs = $this->em->getRepository(Natinf::class)->findAllByStatus(true);
        $natinfsFiltered = $this->em->getRepository(Natinf::class)->findNatinfsByFolder($folder);

        $form = $this->createForm(FolderStep3Type::class, $folder, ['natinfsFiltered' => $natinfsFiltered]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($folder);
            $this->em->flush();

            $this->addFlash('success', 'flash.folder.step3Saved');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return $this->render('@LuccaFolder/Folder/step3.html.twig', [
            'folder' => $folder,
            'minute' => $minute,
            'natinfs' => $natinfs,
            'natinfsFiltered' => $natinfsFiltered,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Folder entity.
     *
     * @throws ORMException
     */
    #[Route(path: '-{id}/edit', name: 'lucca_folder_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Request $request,
        Folder $folder
    ): Response
    {
        $editForm = $this->createForm(FolderType::class, $folder, [
            'minute' => $minute,
        ]);

        $editForm->remove('control');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** Create / update / delete editions if needed */
            $this->folderEditionManager->manageEditionsOnFormSubmission($folder);

            /** Config auto to Obstacle folder */
            if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
                $this->folderManager->configureObstacleFolder($folder);
            }

            /** update status of the minute */
            $this->minuteStoryManager->manage($minute);

            // TODO Fix the collection and remove this temp fix
            foreach ($editForm->get('elements')->getData() as $element) {
                $element->setFolder($folder);
            }

            $folder->setElements($editForm->get('elements')->getData());

            $this->em->persist($folder);
            $this->em->flush();

            $this->addFlash('info', 'flash.folder.updatedSuccessfully');

            if ($request->request->get('saveAndContinue') !== null) {
                return $this->redirectToRoute('lucca_folder_step1', [
                    'minute_id' => $minute->getId(),
                    'id' => $folder->getId(),
                ]);
            }

            return $this->redirectToRoute('lucca_minute_show', [
                'id' => $minute->getId(),
                '_fragment' => 'folder-' . $folder->getId(),
            ]);
        }

        return $this->render('@LuccaFolder/Folder/edit.html.twig', [
            'folder' => $folder,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'user' => $minute->getAdherent()->getUser(),
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Deletes a Folder entity.
     *
     * @throws ORMException
     */
    #[Route(path: '-{id}/delete', name: 'lucca_folder_delete', methods: ['GET', 'DELETE'])]
    #[IsGranted('ROLE_LUCCA')]
    public function deleteAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder,
    ): Response
    {
        $folder->getControl()->setIsFenced(false);
        $folder->getControl()->setFolder(null);

        $this->em->persist($folder->getControl());
        $this->em->remove($folder);
        $this->em->flush();

        /** update status of the minute */
        $this->minuteStoryManager->manage($minute);

        $this->em->flush();

        $this->addFlash('success', 'flash.folder.deletedSuccessfully');

        return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
    }

    /**
     * Creates a form to delete a Folder entity.
     */
    private function createDeleteForm(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder,
    ): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_folder_delete', ['minute_id' => $minute->getId(), 'id' => $folder->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Close a Folder entity.
     *
     * @throws Exception
     */
    #[Route(path: '-{id}/close', name: 'lucca_folder_fence')]
    #[IsGranted('ROLE_LUCCA')]
    public function fenceAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder,
    ): Response
    {
        $this->folderManager->closeFolder($folder);

        $this->em->flush();

        /** update status of the minute */
        $this->minuteStoryManager->manage($minute);

        $this->em->flush();

        $this->addFlash('success', 'flash.folder.closeSuccessfully');

        return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
    }

    /**
     * Open a Folder entity.
     */
    #[Route(path: '-{id}/open', name: 'lucca_folder_open')]
    #[IsGranted('ROLE_FOLDER_OPEN')]
    public function openAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder,
    ): Response
    {
        $folder->setDateClosure(null);
        $folder->getControl()->setIsFenced(false);

        $this->em->persist($folder);
        $this->em->persist($folder->getControl());
        $this->em->flush();

        $this->addFlash('info', 'flash.folder.openSuccessfully');

        return $this->redirectToRoute('lucca_minute_show', [
            'id' => $minute->getId(),
            '_fragment' => 'folder-' . $folder->getId(),
        ]);
    }

    /**
     * Open a Folder entity.
     */
    #[Route(path: '-{id}/reread', name: 'lucca_folder_reread')]
    #[Route(path: '-{id}/unreread', name: 'lucca_folder_unreread')]
    #[IsGranted('ROLE_ADMIN')]
    public function rereadAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder,
    ): Response
    {
        if ($folder->getIsReReaded()) {
            $folder->setIsReReaded(false);
            $this->addFlash('info', 'flash.folder.markAsReReaded');
        } else {
            $folder->setIsReReaded(true);
            $this->addFlash('info', 'flash.folder.markAsUnReReaded');
        }

        $this->em->persist($folder);
        $this->em->flush();

        return $this->redirectToRoute('lucca_minute_show', [
            'id' => $minute->getId(),
            '_fragment' => 'folder-' . $folder->getId(),
        ]);
    }
}
