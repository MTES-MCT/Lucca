<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\ByFolder;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\FolderBundle\Form\ByFolder\FolderSignedType;

/**
 * Class FolderSignedController
 *
 * @package Lucca\Bundle\FolderBundle\Controller\ByFolder
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
#[Route('/minute-{minute_id}/folder-{folder_id}/folderSigned')]
#[IsGranted('ROLE_USER')]
class FolderSignedController extends AbstractController
{

    /**
     * Creates a new FolderSigned entity.
     *
     * @param Request $request
     * @param Minute $p_minute
     * @param Folder $p_folder
     * @return RedirectResponse|Response
     */
    #[Route('/new', name: 'lucca_folderSigned_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $p_minute,
        #[MapEntity(id: 'folder_id')] Folder $p_folder
    ): RedirectResponse|Response {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(FolderSignedType::class, $p_folder);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($p_folder);
            $em->flush();

            $this->addFlash('success', 'flash.folderSigned.createdSuccessfully');
            return $this->redirectToRoute('lucca_minute_show', array(
                'id' => $p_minute->getId()
            ));
        }

        return $this->render('@LuccaMinute/FolderSigned/ByFolder/new.html.twig', array(
            'minute' => $p_minute,
            'folder' => $p_folder,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing FolderSigned entity.
     *
     * @param Request $request
     * @param Minute $p_minute
     * @param Folder $p_folder
     * @return RedirectResponse|Response
     */
    #[Route('/edit', name: 'lucca_folderSigned_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $p_minute,
        #[MapEntity(id: 'folder_id')] Folder $p_folder
    ): RedirectResponse|Response {
        $editForm = $this->createForm(FolderSignedType::class, $p_folder);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($p_folder);
            $em->flush();

            $this->addFlash('info', 'flash.folderSigned.updatedSuccessfully');
            return $this->redirectToRoute('lucca_minute_show', array(
                'id' => $p_minute->getId()
            ));
        }

        return $this->render('@LuccaMinute/FolderSigned/ByFolder/edit.html.twig', array(
            'minute' => $p_minute,
            'folder' => $p_folder,
            'edit_form' => $editForm->createView(),
        ));
    }
}
