<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Utils;

use GdImage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Lucca\Bundle\MediaBundle\Entity\Media;

readonly class ImageResizer
{
    public function __construct(
        private FormatDecisionMaker   $formatDecisionMaker,
        private ParameterBagInterface $params,
    )
    {
    }

    /**
     * Resize width if media is identified like an image
     */
    public function resizeWidth(Media $media, $newWidth): false|GdImage
    {
        /** Start by check if media can be resize ;) */
        if (!in_array($media->getMimeType(), $this->formatDecisionMaker->getMimes('image'), true)) {
            return false;
        }

        $filePath = $this->params->get('lucca_media.upload_directory') . $media->getFilePath();
        $imageSize = getimagesize($filePath);

        $originalWidth = $imageSize[0];
        $originalHeight = $imageSize[1];

        if ($newWidth != $originalWidth) {
            $ratio = $newWidth / $originalWidth;
            $newHeight = $originalHeight * $ratio;

            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            $imageCopy = $this->imageCreateAccordingToMime($filePath, $imageSize);

            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            imagecopyresampled($newImage, $imageCopy, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            return $newImage;
        }

        return false;
    }

    /** TODO Use ImageMagick to keep gif animated */
    public function imageCreateAccordingToMime($file, $size): false|GdImage
    {
        return match ($size["mime"]) {
            "image/jpeg" => imagecreatefromjpeg($file),
            "image/jpg" => imagecreatefromjpeg($file),
            "image/gif" => imagecreatefromgif($file),
            "image/png" => imagecreatefrompng($file),
            default => false,
        };
    }

    public function imagePrintAccordingToMime(Media $media, $imageResized): bool
    {
        return match ($media->getMimeType()) {
            "image/jpeg" => imagejpeg($imageResized),
            "image/jpg" => imagejpeg($imageResized),
            "image/gif" => imagegif($imageResized),
            "image/png" => imagepng($imageResized),
            default => false,
        };
    }
}
