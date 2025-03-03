<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\ManualEdit;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\FolderBundle\Form\Folder\FolderEditionType;
use Lucca\Bundle\SettingBundle\Utils\SettingManager;

/**
 * Class FolderController
 *
 * @package Lucca\Bundle\FolderBundle\Controller\ManualEdit
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
#[IsGranted('ROLE_LUCCA')]
#[Route('/minute-{minute_id}/folder-')]
class FolderController extends AbstractController
{
    /** Setting if use agent of refresh or minute agent */
    private $useRefreshAgentForRefreshSignature;

    public function __construct()
    {
        $this->useRefreshAgentForRefreshSignature = SettingManager::get('setting.folder.useRefreshAgentForRefreshSignature.name');
    }

    /**
     * Displays an folder Edition
     *
     * @Route("{id}/edit-manually", name="lucca_folder_manual", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response
     */
    #[Route('{id}/edit-manually', name: 'lucca_folder_manual', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function manualEditingAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'id')] Folder $folder
    ): RedirectResponse|Response {
        if ($folder->getDateClosure()) {
            $this->addFlash('warning', 'flash.folder.alreadyFenced');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $update = $em->getRepository('LuccaFolderBundle:Updating')->findUpdatingByControl($folder->getControl());

        $edition = $folder->getEdition();
        $logo = $this->get('lucca.utils.printer.control')->defineLogo($minute->getAdherent());

        if ($this->useRefreshAgentForRefreshSignature && null !== $update)
            $agent = $update->getControls()->first()->getAgent();
        else
            $agent = $minute->getAgent();

        /** Fill empty edition with basic view */
        if (!$edition->getFolderEdited()) {

            if ($folder->getNature() === Folder::NATURE_OBSTACLE) {
                if (SettingManager::get('setting.folder.docContentObstacle.name')) {
                    $docName = '@LuccaFolder/Folder/Printing/Custom:doc_obstacle_content-' .
                        SettingManager::get('setting.general.departement.name') . '.html.twig';
                } else {
                    $docName = '@LuccaFolder/Folder/Printing/Basic/doc_obstacle_content.html.twig';
                }
            } else {
                if (SettingManager::get('setting.folder.docContent.name')) {
                    $docName = '@LuccaFolder/Folder/Printing/Custom:doc_content-' .
                        SettingManager::get('setting.general.departement.name') . '.html.twig';
                } else {
                    $docName = '@LuccaFolder/Folder/Printing/Basic/doc_content.html.twig';
                }
            }

            $edition->setFolderVersion(
                $this->renderView($docName, array(
                    'agent' => $agent,
                    'minute' => $minute,
                    'folder' => $folder,
                    'update' => $update,
                    'adherent' => $minute->getAdherent(),
                    'officialLogo' => $logo,
                ))
            );
        }

        $form = $this->createForm(FolderEditionType::class, $edition, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean letter content if boolean is false */
            if (!$edition->getFolderEdited())
                $edition->setFolderVersion(null);
            else {
                /** Call service to clean all html of this step from useless fonts */
                $edition->setFolderVersion($this->get('lucca.utils.html_cleaner')->removeAllFonts($edition->getFolderVersion()));
            }

            $em->persist($folder);
            $em->flush();

            $this->addFlash('success', 'flash.folder.updatedManualSuccessfully');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('@LuccaFolder/FolderEdition/editVersion.html.twig', array(
            'agent' => $agent,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'folder' => $folder,
            'edition' => $edition,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ));
    }
}
