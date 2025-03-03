<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Controller\Document;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Minute;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class FolderController
 */
#[IsGranted('ROLE_LUCCA')]
#[Route('/minute-{minute_id}/folder-')]
class FolderController extends AbstractController
{
    /**
     * Displays a Folder
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return Response
     */
    #[Route("{id}/document", name: "lucca_folder_doc", methods: ["GET"])]
    #[IsGranted('ROLE_LUCCA')]
    public function folderDocAction(Minute $minute, Folder $folder): Response
    {
        $em = $this->getDoctrine()->getManager();

        // TODO Check performance bug
//        $folder = $em->getRepository('LuccaDecisionBundle:Folder')->findCompleteFolder($folder);
        $update = $em->getRepository('LuccaDecisionBundle:Updating')->findUpdatingByControl($folder->getControl());

        return $this->render('@LuccaDecision/Printing/Basic/doc.html.twig', array(
            'minute' => $minute,
            'folder' => $folder,
            'update' => $update,
            'adherent' => $minute->getAdherent(),
        ));
    }
}
