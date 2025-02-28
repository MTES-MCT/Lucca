<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Document;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FolderController
 *
 * @Route("/minute-{minute_id}/folder-")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Folderler\Document
 * @author Terence <terence@numeric-wave.tech>
 */
class FolderController extends Controller
{
    /**
     * Displays a Folder
     *
     * @Route("{id}/document", name="lucca_folder_doc", methods={"GET"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return Response
     */
    public function folderDocAction(Minute $minute, Folder $folder)
    {
        $em = $this->getDoctrine()->getManager();

        // TODO Check performance bug
//        $folder = $em->getRepository('LuccaMinuteBundle:Folder')->findCompleteFolder($folder);
        $update = $em->getRepository('LuccaMinuteBundle:Updating')->findUpdatingByControl($folder->getControl());

        return $this->render('LuccaMinuteBundle:Folder/Printing/Basic:doc.html.twig', array(
            'minute' => $minute,
            'folder' => $folder,
            'update' => $update,
            'adherent' => $minute->getAdherent(),
        ));
    }
}
