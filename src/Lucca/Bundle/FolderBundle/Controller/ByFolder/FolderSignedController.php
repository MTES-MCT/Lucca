<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\ByFolder;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\FolderBundle\Form\ByFolder\FolderSignedType;
use Lucca\Bundle\MinuteBundle\Entity\Minute;

#[Route(path: '/minute-{minute_id}/folder-{folder_id}/folderSigned')]
#[IsGranted('ROLE_USER')]
class FolderSignedController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * Creates a new FolderSigned entity.
     */
    #[Route(path: '/new', name: 'lucca_folderSigned_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(
        Request                              $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'folder_id')] Folder $folder
    ): Response {
        $form = $this->createForm(FolderSignedType::class, $folder);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($folder);
            $this->em->flush();

            $this->addFlash('success', 'flash.folderSigned.createdSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', [
                'id' => $minute->getId()
            ]);
        }

        return $this->render('@LuccaFolder/FolderSigned/ByFolder/new.html.twig', [
            'minute' => $minute,
            'folder' => $folder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing FolderSigned entity.
     */
    #[Route(path: '/edit', name: 'lucca_folderSigned_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(
        Request                              $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'folder_id')] Folder $folder
    ): Response {
        $editForm = $this->createForm(FolderSignedType::class, $folder);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($folder);
            $this->em->flush();

            $this->addFlash('success', 'flash.folderSigned.updatedSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', [
                'id' => $minute->getId()
            ]);
        }

        return $this->render('@LuccaFolder/FolderSigned/ByFolder/edit.html.twig', [
            'minute' => $minute,
            'folder' => $folder,
            'edit_form' => $editForm->createView(),
        ]);
    }
}
