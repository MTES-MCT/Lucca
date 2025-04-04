<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Repository;

use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;

class IntercommunalRepository extends LuccaRepository
{
    /**
     * @Override - FindAll
     * Join with intercommunal
     */
    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('intercommunal')
            ->leftJoin('intercommunal.office', 'office')
            ->addSelect('office');

        return $qb->getQuery()->getResult();
    }
}
