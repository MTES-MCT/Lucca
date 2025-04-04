<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface MediaListAsyncInterface
{
    /**
     * Add media by asynchronous method.
     */
    public function addAsyncMedia(Media $media, string $vars = null): self;

    /**
     * Remove media by asynchronous method.
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAsyncMedia(Media $media, string $vars = null): bool;

    /**
     * Get medias by asynchronous method.
     */
    public function getAsyncMedias(): Collection;
}
