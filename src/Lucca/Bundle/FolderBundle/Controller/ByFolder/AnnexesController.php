<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\ByFolder;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Lucca\MinuteBundle\Form\ByFolder\AnnexesType;
use Lucca\SettingBundle\Utils\SettingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AnnexesController
 *
 * @Route("/minute-{minute_id}/folder-{folder_id}/annexes")
 * @Security("has_role('ROLE_USER')")
 * @ParamConverter("p_minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 * @ParamConverter("p_folder", class="LuccaMinuteBundle:Folder", options={"id" = "folder_id"})
 *
 * @package Lucca\MinuteBundle\Controller\ByFolder
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class AnnexesController extends Controller
{
    /**
     * Displays a form to edit Annexes in dropzone.
     *
     * @Route("/edit", name="lucca_annexes_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $p_minute
     * @param Folder $p_folder
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Minute $p_minute, Folder $p_folder)
    {
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
