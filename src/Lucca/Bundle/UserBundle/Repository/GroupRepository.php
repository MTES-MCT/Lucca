<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Repository;

use Doctrine\ORM\{EntityRepository, QueryBuilder};

class GroupRepository extends EntityRepository
{
    /**
     * Get form who can be displayed
     */
    public function getFormGroup(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('groups');

        $qb->where($qb->expr()->eq('groups.displayed', ':q_displayed'))
            ->setParameter(':q_displayed', true);

        return $qb;
    }
}
