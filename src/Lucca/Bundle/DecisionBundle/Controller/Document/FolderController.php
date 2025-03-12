<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Controller\Document;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\{Minute, Updating};

#[IsGranted('ROLE_LUCCA')]
#[Route(path: '/minute-{minute_id}/folder-')]
class FolderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * Displays a Folder
     */
    #[Route("{id}/document", name: "lucca_folder_doc", methods: ["GET"])]
    #[IsGranted('ROLE_LUCCA')]
    public function folderDocAction(
        Folder $folder,
        #[MapEntity(id: 'minute_id')] Minute $minute,
    ): Response
    {
        // TODO Check performance issue
//        $folder = $this->em->getRepository(Folder::class)->findCompleteFolder($folder);
        $update = $this->em->getRepository(Updating::class)->findUpdatingByControl($folder->getControl());

        return $this->render('@LuccaDecision/Printing/Basic/doc.html.twig', [
            'minute' => $minute,
            'folder' => $folder,
            'update' => $update,
            'adherent' => $minute->getAdherent(),
        ]);
    }
}
