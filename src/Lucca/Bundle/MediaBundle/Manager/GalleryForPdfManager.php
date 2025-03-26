<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Manager;

use Lucca\Bundle\MediaBundle\Entity\Gallery;

readonly class GalleryForPdfManager
{
    /**
     * Manage Gallery
     */
    public function manageGalleryForPdf(Gallery $p_gallery): array
    {
        $defaultImage = null;
        $aMediasImages = [];

        if($p_gallery->getDefaultMedia() !== null) {
            $isImage = str_contains($p_gallery->getDefaultMedia()->getMimeType(), 'image/');
            if ($isImage) {
                $defaultImage = $p_gallery->getDefaultMedia();
            }
        }

        if($p_gallery->getMedias() !== null) {
            foreach ($p_gallery->getMedias() as $medias) {
                $isImage = str_contains($medias->getMimeType(), 'image/');
                if ($isImage) {
                    $aMediasImages[] = $medias;
                }
            }
        }

        /** limit the number of images to 3 and shuffle them */
        if(count($aMediasImages) > 2) {
            shuffle($aMediasImages);
            if ($defaultImage !== null) {
                array_unshift($aMediasImages, $defaultImage);
            }
            $aMediasImages = array_slice($aMediasImages, 0, 3);
        }

        return $aMediasImages;
    }
}
