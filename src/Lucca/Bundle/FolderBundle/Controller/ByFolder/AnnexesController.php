<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\ByFolder;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\FolderBundle\Form\ByFolder\AnnexesType;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

#[Route(path: '/minute-{minute_id}/folder-{folder_id}/annexes')]
#[IsGranted('ROLE_USER')]
class AnnexesController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {
    }

    /**
     * Displays a form to edit Annexes in dropzone.
     */
    #[Route(path: '/edit', name: 'lucca_annexes_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(
        Request                              $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'folder_id')] Folder $object
    ): Response {
        /** Check if the user can access to the annexes functionality */
        if (SettingManager::get('setting.module.annexes.name') == false) {
            $this->addFlash('danger', 'flash.adherent.accessDenied');

            return $this->redirectToRoute('lucca_core_dashboard');
        }

        $editForm = $this->createForm(AnnexesType::class, $object);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($object);
            $this->em->flush();

            $this->addFlash('info', 'flash.annexes.updatedSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', [
                'id' => $minute->getId()
            ]);
        }

        return $this->render('@LuccaFolder/Annexes/ByFolder/edit.html.twig', [
            'minute' => $minute,
            'folder' => $object,
            'maxFilesize' => SettingManager::get('setting.folder.annexesMaxSize.name'),
            'maxCollectionFiles' => SettingManager::get('setting.folder.annexesQuantity.name'),
            'edit_form' => $editForm->createView(),
        ]);
    }
}
