<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\LogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

use Doctrine\ORM\QueryBuilder;
use Lucca\Bundle\UserBundle\Entity\User;

class LogRepository extends EntityRepository
{
    /**
     * Find all log for one user
     */
    public function findAllByUser(User $user): array
    {
        $qb = $this->queryLog();

        $qb->where($qb->expr()->eq('user', ':user'))
            ->setParameter('user', $user);

        $qb->orderBy('log.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all log for object's class
     */
    public function findAllByClass($entity): array
    {
        $qb = $this->queryLog();

        $qb->where($qb->expr()->eq('log.classname', ':q_class'))
            ->setParameter('q_class', get_class($entity));

        $qb->orderBy('log.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all log for object'
     */
    public function findAllByObject($entity): array
    {
        $qb = $this->queryLog();

        $qb->where($qb->expr()->eq('log.classname', ':q_class'))
            ->andWhere($qb->expr()->eq('log.objectId', ':q_id'))
            ->setParameter('q_class', get_class($entity))
            ->setParameter('q_id', $entity->getId());

        $qb->orderBy('log.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Browse Product *****/
    /*******************************************************************************************/

    public function getDatatableData(string  $orderColumn, string $orderDirection, ?string $start = '0', ?string $length = '10',
                                     ?string $status = null, ?string $shortMessage = null): array
    {
        $recordsQB = $this->createQueryBuilder('log')
            ->select('COUNT(log)');

        $qb = $this->queryLog();


        $qb->select([
            'partial log.{id, status, shortMessage, classname, objectId, createdAt}',
            'partial user.{id, username}',
        ]);

        if ($status) {
            $qb->andWhere($qb->expr()->eq('log.status', ':q_status'))
                ->setParameter('q_status', $status);

            $recordsQB->andWhere($recordsQB->expr()->eq('log.status', ':q_status'))
                ->setParameter('q_status', $status);
        }

        if ($shortMessage) {
            $qb->andWhere($qb->expr()->like('log.shortMessage', ':q_shortMessage'))
                ->setParameter('q_shortMessage', '%' . $shortMessage . '%');

            $recordsQB->andWhere($recordsQB->expr()->like('log.shortMessage', ':q_shortMessage'))
                ->setParameter('q_shortMessage', '%' . $shortMessage . '%');

        }

        $recordsQB = $recordsQB->getQuery()->getSingleScalarResult();

        $start = intval($start);
        $length = intval($length);

        $qb->setFirstResult($start)
            ->setMaxResults($length)
            ->orderBy("log.$orderColumn", $orderDirection);

        $data = $qb->getQuery()->getArrayResult();


        $qb = $this->createQueryBuilder('log')
            ->select("COUNT(log)");

        $count = $qb->getQuery()->getSingleScalarResult();

        return ['count' => $count, 'data' => $data, 'recordsFiltered' => $recordsQB];
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Log dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryLog();
        $qb->orderBy('log.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Log dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryLog();

        $qb->where($qb->expr()->eq('log.id', ':q_log'))
            ->setParameter(':q_log', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Log Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Log Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryLog(): QueryBuilder
    {
        return $this->createQueryBuilder('log')
            ->leftJoin('log.user', 'user')->addSelect('user');
    }
}
