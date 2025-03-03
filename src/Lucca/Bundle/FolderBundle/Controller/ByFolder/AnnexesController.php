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
use Lucca\Bundle\FolderBundle\Form\ByFolder\AnnexesType;
use Lucca\Bundle\SettingBundle\Utils\SettingManager;

/**
 * Class AnnexesController
 * @package Lucca\Bundle\FolderBundle\Controller\ByFolder
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
#[Route('/minute-{minute_id}/folder-{folder_id}/annexes')]
#[IsGranted('ROLE_USER')]
class AnnexesController extends AbstractController
{
    /**
     * Displays a form to edit Annexes in dropzone.
     *
     * @param Request $request
     * @param Minute $p_minute
     * @param Folder $p_folder
     * @return RedirectResponse|Response
     */
    #[Route('/edit', name: 'lucca_annexes_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $p_minute,
        #[MapEntity(id: 'folder_id')] Folder $p_folder
    ): RedirectResponse|Response {
        /** Check if the user can access to the annexes functionality */
        if(SettingManager::get('setting.module.annexes.name') == false){
            $this->addFlash('danger', 'flash.adherent.accessDenied');
            return $this->redirectToRoute('lucca_core_dashboard');
        }

        $editForm = $this->createForm(AnnexesType::class, $p_folder);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($p_folder);
            $em->flush();

            $this->addFlash('info', 'flash.annexes.updatedSuccessfully');
            return $this->redirectToRoute('lucca_minute_show', array(
                'id' => $p_minute->getId()
            ));
        }

        return $this->render('@LuccaMinute/Annexes/ByFolder/edit.html.twig', array(
            'minute' => $p_minute,
            'folder' => $p_folder,
            'maxFilesize' => SettingManager::get('setting.folder.annexesMaxSize.name'),
            'maxCollectionFiles' => SettingManager::get('setting.folder.annexesQuantity.name'),
            'edit_form' => $editForm->createView(),
        ));
    }
}
