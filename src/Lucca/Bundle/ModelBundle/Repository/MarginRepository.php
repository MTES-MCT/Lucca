<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Repository;

use Doctrine\ORM\{NonUniqueResultException, QueryBuilder};

use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;
use Lucca\Bundle\ModelBundle\Entity\Page;

class MarginRepository extends LuccaRepository
{
    /**
     * Get Margins available
     */
    public function getMarginAvailableByPage(Page $p_page = null): QueryBuilder
    {
        $qb = $this->queryMargin();

        if ($p_page != null) {
            $qb->where($qb->expr()->eq('margin.page', ':q_page'))
                ->setParameter(':q_page', $p_page);
        }

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Margin dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryMargin();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Margin dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryMargin();

        $qb->where($qb->expr()->eq('margin.id', ':q_margin'))
            ->setParameter(':q_margin', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Margin Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Margin Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    public function queryMargin(): QueryBuilder
    {
        return $this->createQueryBuilder('margin')
            ->leftJoin('margin.blocs', 'blocs')->addSelect('blocs')
        ;
    }
}
