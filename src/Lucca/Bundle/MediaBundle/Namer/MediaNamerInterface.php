<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Namer;

use Lucca\Bundle\MediaBundle\Entity\Media;

interface MediaNamerInterface
{
    /**
     * Service constant to Media
     * CAREFUL -- All of these name is declared in service.yml in MediaBundle
     */
    const NAMER_MEDIA = 'lucca.namer.media';

    /**
     * Create different name for a Media Entity
     * Each service can have a different logic
     */
    public function createMediaName(Media $media, $filename): mixed;
}
