<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\RenderApi;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Updating;

#[IsGranted('ROLE_LUCCA')]
class UpdatingWidgetController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[IsGranted('ROLE_LUCCA')]
    public function displayControlsAndFoldersAction(
        #[MapEntity(id: 'updating_id')] Updating $updating,
    ): Response
    {
        $em = $this->entityManager;;

        /** If form is not submitted - find Control and Folder on this Minute */
        $controls = $em->getRepository(Control::class)->findByMinute($updating->getMinute());
        $folders = $em->getRepository(Folder::class)->findByMinute($updating->getMinute());

        return $this->render('@LuccaMinute/RenderApi/controlsAndFolders.html.twig', array(
            'loopControls' => $controls,
            'loopFolders' => $folders,
        ));
    }
}
