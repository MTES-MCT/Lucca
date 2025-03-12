<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Repository;

use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;

class PageRepository extends LuccaRepository
{
    /**
     * Override FindAll
     * With join on subarea
     */
    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('page')
            ->leftJoin('page.mediasLinked', 'mediasLinked')->addSelect('mediasLinked')
            ->leftJoin('page.subarea', 'subarea')->addSelect('subarea');

        $qb->orderBy('subarea.position, page.position', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
