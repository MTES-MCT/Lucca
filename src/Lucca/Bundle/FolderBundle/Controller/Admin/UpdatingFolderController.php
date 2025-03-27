<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
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

use Lucca\Bundle\FolderBundle\Entity\{Courier, Folder};
use Lucca\Bundle\FolderBundle\Form\{FolderType, UpdatingFolderType};
use Lucca\Bundle\FolderBundle\Generator\NumFolderGenerator;
use Lucca\Bundle\FolderBundle\Utils\{CourierEditionManager, FolderEditionManager, FolderManager};
use Lucca\Bundle\MinuteBundle\Manager\MinuteStoryManager;
use Lucca\Bundle\MinuteBundle\Entity\{Minute, Updating};

#[IsGranted('ROLE_LUCCA')]
#[Route(path: '/minute-{minute_id}/updating-{updating_id}/folder')]
class UpdatingFolderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FolderManager $folderManager,
        private readonly FolderEditionManager $folderEditionManager,
        private readonly MinuteStoryManager $minuteStoryManager,
        private readonly NumFolderGenerator $numFolderGenerator,
        private readonly CourierEditionManager $courierEditionManager,
    )
    {
    }

    /**
     * Creates a new Folder entity.
     *
     * @throws ORMException
     */
    #[Route(path: '/new', name: 'lucca_updating_folder_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'updating_id')] Updating $updating,
        Request $request
    ): Response {
        $folder = new Folder();

        $folder->setMinute($minute);
        $folder->setType(Folder::TYPE_REFRESH);

        /** Add all default element to checklist */
        $folder = $this->folderManager->addChecklistToFolder($folder);

        /** Create form */
        $form = $this->createForm(UpdatingFolderType::class, $folder, [
            'minute' => $minute, 'updating' => $updating,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** If there is no control linked to the folder do nothing and display an error message */
            if (!$folder->getControl()) {
                $this->addFlash('danger', 'flash.control.needed');
            } else {
                /** Create folder num */
                $folder->setNum($this->numFolderGenerator->generate($folder));

                /** Create / update / delete editions if needed */
                $this->folderEditionManager->manageEditionsOnFormSubmission($folder);

                /** update status of the minute */
                $this->minuteStoryManager->manage($minute);

                /** Persist folder */
                $this->em->persist($folder);
                $this->em->flush();

                $this->addFlash('success', 'flash.folder.createdSuccessfully');
                return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
            }
        }

        return $this->render('@LuccaFolder/Folder/new.html.twig', [
            'updating' => $updating,
            'folder' => $folder,
            'minute' => $minute,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Folder entity.
     */
    #[Route(path: '-{id}/edit', name: 'lucca_updating_folder_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder
    ): Response {
        $editForm = $this->createForm(FolderType::class, $folder, [
            'minute' => $minute,
        ]);

        $editForm->remove('control');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** Create / update / delete editions if needed */
            $this->folderEditionManager->manageEditionsOnFormSubmission($folder);

            $this->em->persist($folder);
            $this->em->flush();

            $this->minuteStoryManager->manage($minute);
            $this->em->flush();

            $this->addFlash('success', 'flash.folder.updatedSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
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
     */
    #[Route(path: '-{id}/delete', name: 'lucca_updating_folder_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_LUCCA')]
    public function deleteAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder
    ): Response {

        $courier = $this->em->getRepository(Courier::class)->findOneBy([
            'folder' => $folder
        ]);

        foreach ($courier->getHumansEditions() as $edition) {
            $this->em->remove($edition);
        }

        $this->em->remove($courier->getEdition());

        $this->em->remove($folder);
        $this->em->flush();

        $this->addFlash('success', 'flash.folder.deletedSuccessfully');

        return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
    }

    /**
     * Creates a form to delete a Folder entity.
     */
    private function createDeleteForm(Minute $minute, Folder $folder): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_updating_folder_delete', ['minute_id' => $minute->getId(), 'id' => $folder->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Close a Folder entity.
     */
    #[Route(path: '-{id}/close', name: 'lucca_updating_folder_fence', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function fenceAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder,
    ): Response {
        $courier = $this->em->getRepository(Courier::class)->findOneBy([
            'folder' => $folder
        ]);

        if (!$courier) {
            $courier = new Courier();
            $courier->setFolder($folder);

            /** Create / update / delete editions if needed */
            $this->courierEditionManager->manageEditionsOnFormSubmission($courier);

            $this->em->persist($courier);
            $this->addFlash('success', 'flash.courier.addSuccessfully');
        }

        $this->em->persist($folder);
        $this->em->flush();

        $this->addFlash('danger', 'flash.folder.closeSuccessfully');

        return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
    }
}
