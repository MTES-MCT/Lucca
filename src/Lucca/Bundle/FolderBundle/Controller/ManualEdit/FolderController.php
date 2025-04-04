<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\ManualEdit;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\FolderBundle\Form\Folder\FolderEditionType;
use Lucca\Bundle\MinuteBundle\Entity\{Minute, Updating};
use Lucca\Bundle\MinuteBundle\Printer\ControlPrinter;
use Lucca\Bundle\MinuteBundle\Utils\HtmlCleaner;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

#[IsGranted('ROLE_LUCCA')]
#[Route(path: '/minute-{minute_id}/folder-')]
class FolderController extends AbstractController
{
    /** Setting if use agent of refresh or minute agent */
    private $useRefreshAgentForRefreshSignature;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ControlPrinter $controlPrinter,
        private readonly HtmlCleaner $htmlCleaner,
    )
    {
        $this->useRefreshAgentForRefreshSignature = SettingManager::get('setting.folder.useRefreshAgentForRefreshSignature.name');
    }

    /**
     * Displays an folder Edition
     */
    #[Route(path: '{id}/edit-manually', name: 'lucca_folder_manual', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function manualEditingAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder $folder
    ): Response {
        if ($folder->getDateClosure()) {
            $this->addFlash('warning', 'flash.folder.alreadyFenced');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        $update = $this->em->getRepository(Updating::class)->findUpdatingByControl($folder->getControl());

        $edition = $folder->getEdition();
        $logo = $this->controlPrinter->defineLogo($minute->getAdherent());

        if ($this->useRefreshAgentForRefreshSignature && null !== $update) {
            $agent = $update->getControls()->first()->getAgent();
        }
        else {
            $agent = $minute->getAgent();
        }

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
                    $docName = '@LuccaFolder/Folder/Printing/Custom/doc_content-' .
                        SettingManager::get('setting.general.departement.name') . '.html.twig';
                } else {
                    $docName = '@LuccaFolder/Folder/Printing/Basic/doc_content.html.twig';
                }
            }

            $edition->setFolderVersion(
                $this->renderView($docName, [
                    'agent' => $agent,
                    'minute' => $minute,
                    'folder' => $folder,
                    'update' => $update,
                    'adherent' => $minute->getAdherent(),
                    'officialLogo' => $logo,
                ])
            );
        }

        $form = $this->createForm(FolderEditionType::class, $edition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean letter content if boolean is false */
            if (!$edition->getFolderEdited()) {
                $edition->setFolderVersion(null);
            }
            else {
                /** Call service to clean all html of this step from useless fonts */
                $edition->setFolderVersion($this->htmlCleaner->removeAllFonts($edition->getFolderVersion()));
            }

            $this->em->persist($folder);
            $this->em->flush();

            $this->addFlash('success', 'flash.folder.updatedManualSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return $this->render('@LuccaFolder/FolderEdition/editVersion.html.twig', [
            'agent' => $agent,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'folder' => $folder,
            'edition' => $edition,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ]);
    }
}
