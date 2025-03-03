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

class TribunalRepository extends LuccaRepository
{
    /**
     * @Override - FindAll
     * Join with tribunal
     */
    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('tribunal')
            ->leftJoin('tribunal.office', 'office')
            ->addSelect('office');

        return $qb->getQuery()->getResult();
    }
}
