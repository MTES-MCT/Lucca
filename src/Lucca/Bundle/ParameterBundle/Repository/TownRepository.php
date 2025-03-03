<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Repository;

use Doctrine\ORM\{NonUniqueResultException, QueryBuilder};

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;

class TownRepository extends LuccaRepository
{
    /*******************************************************************************************/
    /********************* Find methods *****/
    /*******************************************************************************************/

    /**
     * Find one Town by zipcode (like)
     */
    public function findByZipCode($zipcode): mixed
    {
        $qb = $this->queryTown();

        $qb->where($qb->expr()->eq('town.zipcode', ':q_zipcode'))
            ->setParameter('q_zipcode', $zipcode);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find one Town by name (like)
     */
    public function findOneByName($name): mixed
    {
        $qb = $this->queryTown();

        $qb->where($qb->expr()->eq('town.name', ':q_name'))
            ->setParameter('q_name', $name);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Town Repository - ' . $e->getMessage();

            return false;
        }
    }

    /**
     * Get Town filter by Intercommunal
     */
    public function findAllByIntercommunals(array $intercommunals, array $exclude_towns = []): QueryBuilder
    {
        $qb = $this->queryTown();

        $qb->where($qb->expr()->in('intercommunal', ':q_intercommunals'))
            ->setParameter('q_intercommunals', $intercommunals);

        if (!empty($exclude_towns)) {
            $qb->andWhere($qb->expr()->notIn('town', ':q_exclude_towns'))
                ->setParameter('q_exclude_towns', $exclude_towns);
        }

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Get methods *****/
    /*******************************************************************************************/

    /**
     * Get Town filter by Adherent
     */
    public function getTownByAdherent(Adherent $adherent): QueryBuilder
    {
        $qb = $this->queryTown();

        if ($adherent && $adherent->getTown() != null) {
            $town = $adherent->getTown();

            $qb->where($qb->expr()->eq('town', ':town'))
                ->setParameter('town', $town);

        } elseif ($adherent && $adherent->getIntercommunal() != null) {
            $interco = $adherent->getIntercommunal();

            $qb->where($qb->expr()->eq('intercommunal', ':intercommunal'))
                ->setParameter('intercommunal', $interco);
        }

        return $qb;
    }

    /**
     * Get values of town order by name
     */
    public function getValuesOrderedByName(): QueryBuilder
    {
        $qb = $this->queryTown();

        $qb->orderBy('town.name', 'ASC');

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * @Override - FindAll
     * Join with intercommunal
     */
    public function findAll(): array
    {
        $qb = $this->queryTown();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Adherent dependencies
     */
    public function find(mixed$id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryTown();

        $qb->where($qb->expr()->eq('town.id', ':q_town'))
            ->setParameter(':q_town', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Town Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Department Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryTown(): QueryBuilder
    {
        return $this->createQueryBuilder('town')
            ->leftJoin('town.intercommunal', 'intercommunal')->addSelect('intercommunal')
        ;
    }
}
