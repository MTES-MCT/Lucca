<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Entity;

use DateTime, DateTimeImmutable;

interface TimestampableInterface
{
    public function getCreatedAt(): DateTimeImmutable;

    public function setCreatedAt(DateTimeImmutable $createdAt);

    public function getUpdatedAt(): DateTime;

    public function setUpdatedAt(DateTime $updatedAt);
}

