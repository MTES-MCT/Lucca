<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Repository;

use Doctrine\ORM\{EntityRepository, NonUniqueResultException, NoResultException, QueryBuilder};

class LuccaRepository extends EntityRepository
{
    /**
     * Finds all enabled / disabled entities in the repository
     */
    public function findAllActive(bool $enabled = true): array
    {
        return $this->findBy([
            'enabled' => $enabled
        ]);
    }

    /**
     * Find last code use for mix prefix / year
     *
     * @throws NonUniqueResultException
     */
    public function findMaxCode($year, $prefix): mixed
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->where($qb->expr()->like('entity.id', ':q_code'))
            ->setParameter('q_code', "%$prefix$year%");

        $qb->select($qb->expr()->max('entity.id'));

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Return all enabled / disabled values form entity field in forms.
     */
    public function getValuesActive(bool $enabled = true): QueryBuilder
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->where($qb->expr()->eq('entity.enabled', ':q_state'))
            ->setParameter(':q_state', $enabled);

        return $qb;
    }

    /**
     * Count all enabled / disabled entities in the repository
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countActive(bool $enabled = true): int
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->select($qb->expr()->count('entity'));

        $qb->where($qb->expr()->eq('entity.enabled', ':q_state'))
            ->setParameter(':q_state', $enabled);

        return (int)$qb->getQuery()->getSingleScalarResult();
    }
}
