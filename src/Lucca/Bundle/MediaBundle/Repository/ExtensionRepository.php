<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Lucca\Bundle\CoreBundle\Repository\ToggleableRepository;

class ExtensionRepository extends EntityRepository
{
    /** Traits */
    use ToggleableRepository;
}
