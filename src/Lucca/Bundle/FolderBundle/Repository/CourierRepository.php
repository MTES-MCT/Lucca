<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Repository;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

class CourierRepository extends EntityRepository
{
    public function findCouriersByFolder(Folder $folder): array
    {
        $qb = $this->queryCourier();

        $qb->where($qb->expr()->eq('folder', ':folder'))
            ->setParameter(':folder', $folder);

        $qb->orderBy('courier.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find one Courier for unit test with no dateJudicial
     */
    public function findOneForJudicialTest(): mixed
    {
        $qb = $this->createQueryBuilder('courier')
            ->leftJoin('courier.edition', 'edition')->addSelect('edition');

        $qb->andWhere($qb->expr()->isNull('courier.dateJudicial'));
        $qb->andWhere($qb->expr()->isNotNull('courier.edition'));
        $qb->setMaxResults(1);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Courier Repository - ' . $e->getMessage();

            return false;
        }
    }

    /**
     * Find one Courier for unit test with no dateDdtm
     */
    public function findOneForDdtmTest(): mixed
    {
        $qb = $this->createQueryBuilder('courier')
            ->leftJoin('courier.edition', 'edition')->addSelect('edition');

        $qb->andWhere($qb->expr()->isNull('courier.dateDdtm'));
        $qb->andWhere($qb->expr()->isNotNull('courier.edition'));
        $qb->setMaxResults(1);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Courier Repository - ' . $e->getMessage();

            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Custom find for test methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Courier dependencies
     */
    public function findOneForTest(): mixed
    {
        $qb = $this->queryCourierComplete();

        $qb->where($qb->expr()->isNull('adherent.logo'));
        $qb->where($qb->expr()->isNull('courier.edition'));
        $qb->where($qb->expr()->isNotNull('courier.dateOffender'));

        $qb->setMaxResults(1);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Folder Repository - ' . $e->getMessage();

            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Courier dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryCourier();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Courier dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryCourier();

        $qb->where($qb->expr()->eq('courier.id', ':q_courier'))
            ->setParameter(':q_courier', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Courier Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Courier Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryCourier(): QueryBuilder
    {
        return $this->createQueryBuilder('courier')
            ->leftJoin('courier.folder', 'folder')->addSelect('folder')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute')
            ->leftJoin('courier.edition', 'edition')->addSelect('edition')
            ->leftJoin('courier.humansEditions', 'humansEditions')->addSelect('humansEditions')
            ->leftJoin('humansEditions.human', 'human')->addSelect('human');
    }

    /**
     * Classic dependencies
     */
    private function queryCourierComplete(): QueryBuilder
    {
        return $this->createQueryBuilder('courier')
            ->leftJoin('courier.folder', 'folder')->addSelect('folder')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute')
            ->leftJoin('minute.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('courier.edition', 'edition')->addSelect('edition')
            ->leftJoin('courier.humansEditions', 'humansEditions')->addSelect('humansEditions')
            ->leftJoin('humansEditions.human', 'human')->addSelect('human');
    }
}
