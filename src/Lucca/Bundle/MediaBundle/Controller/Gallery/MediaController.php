<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Controller\Gallery;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

use Lucca\Bundle\MediaBundle\Entity\{Media, Gallery};
use Lucca\Bundle\MediaBundle\Manager\FileManager;

#[Route(path: '/gallery')]
#[IsGranted('ROLE_ADMIN')]
class MediaController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileManager $fileManager,
    )
    {
    }

    /**
     * Deletes a Media entity.
     */
    #[Route(path: '/media-{id}/delete', name: 'lucca_media_by_gallery_delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MEDIA_TOTAL')]
    public function deleteAction(Media $media): Response
    {
        /** Find if Media is used by a Gallery */
        $gallery = $this->em->getRepository(Gallery::class)->findWithThisMedia($media);

        if ($gallery) {
            $gallery->removeMedia($media);
            $this->fileManager->removeFile($media);

            $this->em->remove($media);
            $this->em->flush();

            $this->addFlash('success', 'flash.media.deletedSuccessfully');
        } else {
            $this->addFlash('warning', 'flash.media.unusedInGallery');
        }

        return $this->redirectToRoute('lucca_media_gallery_show', ['id' => $gallery->getId()]);
    }
}
