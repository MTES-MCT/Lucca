<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Repository;

use Doctrine\ORM\{EntityRepository, NonUniqueResultException, QueryBuilder};

class UserRepository extends EntityRepository
{
    /**
     * Find User who can authenticated
     * By username or email
     */
    public function findUserWhoCanAuthenticated($login): mixed
    {
        $qb = $this->queryUser();

        $qb->where($qb->expr()->eq('user.enabled', ':q_enabled'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->eq('user.username', ':q_login'),
                $qb->expr()->eq('user.email', ':q_login')
            ))
            ->setParameter(':q_enabled', true)
            ->setParameter(':q_login', $login);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with User dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryUser();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with User dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryUser();

        $qb->where($qb->expr()->eq('user.id', ':q_user'))
            ->setParameter(':q_user', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - User Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of User Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryUser(): QueryBuilder
    {
        return $this->createQueryBuilder('user')
            ->leftJoin('user.groups', 'groups')->addSelect('groups');
    }
}
