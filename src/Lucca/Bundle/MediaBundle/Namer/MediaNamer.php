<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Namer;

use Lucca\Bundle\CoreBundle\Utils\Canonalizer;
use Lucca\Bundle\MediaBundle\Entity\Media;

class MediaNamer implements MediaNamerInterface
{
    public function __construct(
        private readonly Canonalizer $canonalizer,
    )
    {
    }

    /**
     * Create all different name of a Media
     */
    public function createMediaName(Media $media, $filename): Media
    {
        /** Application name - written by a User */
        $media->setName($filename);
        /** Original name */
        $media->setNameOriginal($filename);
        /** Canonical name */
        $media->setNameCanonical($this->createMediaCanonicalName($filename));

        return $media;
    }

    /**
     * Create a Canonical name of a Media
     */
    public function createMediaCanonicalName($filename): string
    {
        return uniqid() . "_" . $this->canonalizer->slugify($filename);
    }
}
