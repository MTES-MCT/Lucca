<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Repository;

use Doctrine\ORM\{NonUniqueResultException, QueryBuilder};

use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;
use Lucca\Bundle\UserBundle\Entity\User;

class AdherentRepository extends LuccaRepository
{
    /*******************************************************************************************/
    /********************* Stats methods *****/
    /*******************************************************************************************/

    /**
     * Stat for overall reports
     * Use on overall Stat
     */
    public function statAdherent($adherent = null, $town = null, $interco = null, $service = null): mixed
    {
        $qb = $this->queryAdherent();

        if ($adherent != null && count($adherent) > 0) {
            $qb->andWhere($qb->expr()->in('adherent', ':q_adherent'))
                ->setParameter(':q_adherent', $adherent);
        }

        if ($town != null && count($town) > 0) {
            $qb->andWhere($qb->expr()->in('town', ':q_town'))
                ->setParameter(':q_town', $town);
        }

        if ($interco != null && count($interco) > 0) {
            $qb->andWhere($qb->expr()->in('intercommunal', ':q_intercommunal'))
                ->setParameter(':q_intercommunal', $interco);
        }

        if ($service != null && count($service) > 0) {
            $qb->andWhere($qb->expr()->in('service', ':q_service'))
                ->setParameter(':q_service', $service);
        }

        $qb->select(array(
            'partial adherent.{id, name, firstname, function, service, town, intercommunal, city}',
            'partial town.{id, name}',
        ));

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Custom find methods *****/
    /*******************************************************************************************/

    /**
     * Find max num used for adherent
     * Use on code generator
     */
    public function findMaxUsername($prefix): mixed
    {
        $qb = $this->queryAdherent();

        $qb->where($qb->expr()->like('user.username', ':username'))
            ->setParameter('username', '%' . $prefix . '%');

        $qb->select($qb->expr()->max('user.username'));

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Adherent Repository - ' . $e->getMessage();

            return false;
        }
    }

    /**
     * Find one adherent with email param
     * Used in LuccaImportAdherentCommand
     */
    public function findOneByEmail($email): mixed
    {
        $qb = $this->queryAdherent();

        $qb->where($qb->expr()->eq('user.email', ':email'))
            ->setParameter('email', $email);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Adherent Repository - ' . $e->getMessage();

            return false;
        }
    }

    /**
     * Find one adherent with User entity
     */
    public function findOneByUser(User $user): ?object
    {
        $qb = $this->queryAdherent();

        $qb->where($qb->expr()->eq('user', ':q_user'))
            ->setParameter('q_user', $user);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Adherent Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Custom get methods *****/
    /*******************************************************************************************/

    /**
     * Override function to add join to User Entity
     */
    public function getValuesActive(bool $enabled = true): QueryBuilder
    {
        $qb = $this->queryAdherent();

        $qb->orderBy('adherent.name', 'ASC');

        $qb->where($qb->expr()->eq('adherent.enabled', ':q_enabled'))
            ->setParameter('q_enabled', $enabled);

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Adherent dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryAdherent();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Adherent dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryAdherent();

        $qb->where($qb->expr()->eq('adherent.id', ':q_adherent'))
            ->setParameter(':q_adherent', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Adherent Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Minute Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryAdherent(): QueryBuilder
    {
        return $this->createQueryBuilder('adherent')
            ->leftJoin('adherent.user', 'user')->addSelect('user')
            ->leftJoin('user.groups', 'groups')->addSelect('groups')
            ->leftJoin('adherent.town', 'town')->addSelect('town')
            ->leftJoin('adherent.intercommunal', 'intercommunal')->addSelect('intercommunal')
            ->leftJoin('adherent.service', 'service')->addSelect('service')
        ;
    }
}
