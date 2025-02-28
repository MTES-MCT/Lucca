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
use Lucca\MinuteBundle\Form\ByFolder\FolderSignedType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FolderSignedController
 *
 * @Route("/minute-{minute_id}/folder-{folder_id}/folderSigned")
 * @Security("has_role('ROLE_USER')")
 * @ParamConverter("p_minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 * @ParamConverter("p_folder", class="LuccaMinuteBundle:Folder", options={"id" = "folder_id"})
 *
 * @package Lucca\MinuteBundle\Controller\ByFolder
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class FolderSignedController extends Controller
{

    /**
     * Creates a new FolderSigned entity.
     *
     * @Route("/new", name="lucca_folderSigned_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $p_minute
     * @param Folder $p_folder
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request, Minute $p_minute, Folder $p_folder)
    {
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
     * @Route("/edit", name="lucca_folderSigned_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $p_minute
     * @param Folder $p_folder
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Minute $p_minute, Folder $p_folder)
    {
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
