<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Entity;

interface MediaAsyncInterface
{
    /**
     * Get media by asynchronous method.
     */
    public function getAsyncMedia(): ?Media;

    /**
     * Set Media by asynchronous method.
     */
    public function setAsyncMedia(?Media $media = null): self;
}
