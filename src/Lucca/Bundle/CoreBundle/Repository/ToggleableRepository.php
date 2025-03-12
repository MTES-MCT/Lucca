<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;

trait ToggleableRepository
{
    /**
     * Get all values by status - enabled / disabled
     */
    public function getValuesActive(bool $p_enabled = true): QueryBuilder
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->where($qb->expr()->eq('entity.enabled', ':q_enabled'))
            ->setParameter(':q_enabled', $p_enabled);

        return $qb;
    }
}

