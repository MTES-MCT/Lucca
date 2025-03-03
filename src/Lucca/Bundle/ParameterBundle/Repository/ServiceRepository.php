<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Repository;

use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;

class ServiceRepository extends LuccaRepository
{
    /**
     * @Override - FindAll
     * Join with service
     */
    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('service')
            ->leftJoin('service.office', 'office')
            ->addSelect('office');

        return $qb->getQuery()->getResult();
    }
}
