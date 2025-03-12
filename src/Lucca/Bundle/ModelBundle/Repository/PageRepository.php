<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Repository;

use Doctrine\ORM\{NonUniqueResultException, QueryBuilder};

use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;

class PageRepository extends LuccaRepository
{

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Page dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryPage();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Page dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryPage();

        $qb->where($qb->expr()->eq('page.id', ':q_page'))
            ->setParameter(':q_page', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Page Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Page Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryPage(): QueryBuilder
    {
        return $this->createQueryBuilder('page')
            ->leftJoin('page.marginBottom', 'marginBottom')->addSelect('marginBottom')
            ->leftJoin('page.marginLeft', 'marginLeft')->addSelect('marginLeft')
            ->leftJoin('page.marginRight', 'marginRight')->addSelect('marginRight')
            ->leftJoin('page.marginTop', 'marginTop')->addSelect('marginTop')
        ;
    }
}
