<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Repository;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Class CourierRepository
 *
 * @package Lucca\MinuteBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierRepository extends EntityRepository
{
    /**
     * @param Folder $folder
     * @return array
     */
    public function findCouriersByFolder(Folder $folder)
    {
        $qb = $this->queryCourier();

        $qb->where($qb->expr()->eq('folder', ':folder'))
            ->setParameter(':folder', $folder);

        $qb->orderBy('courier.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find one Courier for unit test with no dateJudicial
     *
     * @return false|int|mixed|string|null
     */
    public function findOneForJudicialTest()
    {
        $qb = $this->createQueryBuilder('courier')
            ->leftJoin('courier.edition', 'edition')->addSelect('edition');

        $qb->andWhere($qb->expr()->isNull('courier.dateJudicial'));
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
     *
     * @return false|int|mixed|string|null
     */
    public function findOneForDdtmTest()
    {
        $qb = $this->createQueryBuilder('courier')
            ->leftJoin('courier.edition', 'edition')->addSelect('edition');

        $qb->andWhere($qb->expr()->isNull('courier.dateDdtm'));
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
     *
     * @return false|int|mixed|string|null
     */
    public function findOneForTest()
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
     *
     * @return array
     */
    public function findAll()
    {
        $qb = $this->queryCourier();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Courier dependencies
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return bool|mixed|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $qb = $this->queryCourier();

        $qb->where($qb->expr()->eq('courier.id', ':q_courier'))
            ->setParameter(':q_courier', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Courier Repository - ' . $e->getMessage();
            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Courier Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     *
     * @return QueryBuilder
     */
    private function queryCourier()
    {
        $qb = $this->createQueryBuilder('courier')
            ->leftJoin('courier.folder', 'folder')->addSelect('folder')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute')
            ->leftJoin('courier.edition', 'edition')->addSelect('edition')
            ->leftJoin('courier.humansEditions', 'humansEditions')->addSelect('humansEditions')
            ->leftJoin('humansEditions.human', 'human')->addSelect('human');

        return $qb;
    }
    /**
     * Classic dependencies
     *
     * @return QueryBuilder
     */
    private function queryCourierComplete()
    {
        $qb = $this->createQueryBuilder('courier')
            ->leftJoin('courier.folder', 'folder')->addSelect('folder')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute')
            ->leftJoin('minute.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('courier.edition', 'edition')->addSelect('edition')
            ->leftJoin('courier.humansEditions', 'humansEditions')->addSelect('humansEditions')
            ->leftJoin('humansEditions.human', 'human')->addSelect('human');

        return $qb;
    }
}
