<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\RenderApi;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Updating;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdatingWidgetController
 *
 * @Route("/updating-{updating_id}/widget-")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("updating", class="LuccaMinuteBundle:Updating", options={"id" = "updating_id"})
 *
 * @package Lucca\MinuteBundle\Controller\RenderApi
 * @author Terence <terence@numeric-wave.tech>
 */
class UpdatingWidgetController extends Controller
{
    /**
     * Find and siplay all Controls and Folders
     *
     * @Route("display-controls-folders", name="lucca_updating_display_controls_folders", methods={"GET"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Updating $updating
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayControlsAndFoldersAction(Updating $updating)
    {
        $em = $this->getDoctrine()->getManager();

        /** If form is not submitted - find Control and Folder on this Minute */
        $controls = $em->getRepository('LuccaMinuteBundle:Control')->findByMinute($updating->getMinute());
        $folders = $em->getRepository('LuccaMinuteBundle:Folder')->findByMinute($updating->getMinute());

        return $this->render('LuccaMinuteBundle:RenderApi:controlsAndFolders.html.twig', array(
            'loopControls' => $controls,
            'loopFolders' => $folders,
        ));
    }
}
