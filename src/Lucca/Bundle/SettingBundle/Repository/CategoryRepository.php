<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Repository;

use Doctrine\ORM\{EntityRepository, NonUniqueResultException, QueryBuilder};

class CategoryRepository extends EntityRepository
{
    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Setting dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryCategory();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Setting dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryCategory();

        $qb->where($qb->expr()->eq('category.id', ':q_category'))
            ->setParameter(':q_category', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Category Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Setting Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryCategory(): QueryBuilder
    {
        return $this->createQueryBuilder('category');
    }
}
