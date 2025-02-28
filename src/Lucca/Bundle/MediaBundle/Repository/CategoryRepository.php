<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Repository;

use Doctrine\ORM\{EntityRepository, NonUniqueResultException, QueryBuilder};

use Lucca\Bundle\MediaBundle\Entity\Category;
use Lucca\Bundle\CoreBundle\Repository\ToggleableRepository;

class CategoryRepository extends EntityRepository
{
    /** Traits */
    use ToggleableRepository;

    /**
     * Find default support
     * Add entities join and select
     */
    public function findDefaultCategory(): mixed
    {
        $qb = $this->queryCategory();

        $qb->where($qb->expr()->eq('category.name', ':q_name'))
            ->setParameter(':q_name', Category::DEFAULT_NAME);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e;
        }
    }

    /**
     * Find one complete Category by extension
     */
    public function findOneCategoryByExtension($extensionValue): mixed
    {
        $qb = $this->queryCategory();

        $qb->where($qb->expr()->eq('extensions.value', ':q_value'))
            ->setParameter(':q_value', $extensionValue);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e;
        }
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Category dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryCategory();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Category dependencies
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
    /********************* Query - Dependencies of Category Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryCategory(): QueryBuilder
    {
        return $this->createQueryBuilder('category')
            ->leftJoin('category.storager', 'storager')->addSelect('storager')
            ->leftJoin('storager.folders', 'folders')->addSelect('folders')
            ->leftJoin('category.extensions', 'extensions')->addSelect('extensions')
            ->leftJoin('category.metasDatasModels', 'mdm')->addSelect('mdm')
        ;
    }
}
