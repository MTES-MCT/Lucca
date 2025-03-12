<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

use Doctrine\ORM\QueryBuilder;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\MinuteBundle\Entity\{Control, Minute};

class UpdatingRepository extends EntityRepository
{
    /**
     * Method used to find all updating with geo code in a specific area and by adherent
     */
    public function findAllInArea($minLat, $maxLat, $minLon, $maxLon, Adherent $adherent = null, $maxResults = null, $minutes = null): mixed
    {
        $qb = $this->getLocalized($adherent);

        $qb->andWhere($qb->expr()->between('plot.latitude', ':q_minLat', ':q_maxLat'))
            ->andWhere($qb->expr()->between('plot.longitude', ':q_minLon', ':q_maxLon'))
            ->setParameter('q_minLat', $minLat)
            ->setParameter('q_maxLat', $maxLat)
            ->setParameter('q_minLon', $minLon)
            ->setParameter('q_maxLon', $maxLon);

        if ($minutes && count($minutes) > 0) {
            $qb->andwhere($qb->expr()->in('updating.minute', ':q_minutes'))
                ->setParameter(':q_minutes', $minutes);
        }

        if ($maxResults){
            $qb->groupBy('updating');
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all updating with geo code and by adherent
     */
    public function findAllWithGeocodeDashboard(?Adherent $p_adherent = null): mixed
    {
        $qb = $this->getLocalized($p_adherent);

        $qb->select('updating.id,
        minute.id as minuteId, minute.num as minuteNum,
        plot.latitude as plotLat, plot.longitude as plotLng, plot.address as plotAddr, plot.place as plotPlace,
        plot.parcel as plotParcel, plot_town.name as plotTownName, plot_town.code as plotTownCode,
        agent.name as agentName, agent.firstname as agentFirstname');

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all updating with geo code and by adherent
     */
    public function findAllWithGeocode(?Adherent $p_adherent = null): mixed
    {
        $qb = $this->getLocalized($p_adherent);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find updating entity by Control
     * used for Folder document
     */
    public function findUpdatingByControl(Control $control): mixed
    {
        $qb = $this->queryUpdating();

        $qb->where($qb->expr()->in('controls', ':q_control'))
            ->setParameter(':q_control', $control);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Updating Repository - ' . $e->getMessage();

            return false;
        }
    }

    /**
     * Find complete Updates associated to Minute
     * use on Minute show
     */
    public function findByMinute(Minute $minute): array
    {
        $qb = $this->queryUpdating();

        $qb->where($qb->expr()->eq('updating.minute', ':q_minute'))
            ->setParameter(':q_minute', $minute);

        $qb->orderBy('updating.num', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find max num used for 1 minute
     * Use on Code generator
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findMaxNumForMinute($prefix): mixed
    {
        $qb = $this->createQueryBuilder('updating');

        $qb->where($qb->expr()->like('updating.num', ':q_num'))
            ->setParameter('q_num', '%' . $prefix . '%');

        $qb->select($qb->expr()->max('updating.num'));

        return $qb->getQuery()->getOneOrNullResult();
    }

    /*******************************************************************************************/
    /********************* Get specifics queries *****/
    /*******************************************************************************************/
    /**
     * Get updating with geo code and with closure date and by adherent
     */
    private function getLocalized(?Adherent $adherent = null): QueryBuilder
    {
        $qb = $this->queryUpdating();

        $qb->where($qb->expr()->isNotNull('plot.latitude'))
            ->andWhere($qb->expr()->isNotNull('plot.longitude'));

        if ($adherent) {
            if ($adherent->getIntercommunal()) {
                $qb->andWhere($qb->expr()->eq('plot_intercommunal', ':q_intercommunal'))
                    ->setParameter('q_intercommunal', $adherent->getIntercommunal());
            }
            elseif ($adherent->getTown()) {
                $qb->andWhere($qb->expr()->eq('plot_town', ':q_town'))
                    ->setParameter(':q_town', $adherent->getTown());
            }
        }

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Updating dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryUpdating();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Updating dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryUpdating();

        $qb->where($qb->expr()->eq('updating.id', ':q_updating'))
            ->setParameter(':q_updating', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Updating Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Updating Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryUpdating(): QueryBuilder
    {
        return $this->createQueryBuilder('updating')
            ->leftJoin('updating.minute', 'minute')->addSelect('minute')
            ->leftJoin('minute.plot', 'plot')->addSelect('plot')
            ->leftJoin('plot.town', 'plot_town')->addSelect('plot_town')
            ->leftJoin('plot_town.intercommunal', 'plot_intercommunal')->addSelect('plot_intercommunal')
            ->leftJoin('minute.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('minute.agent', 'agent')->addSelect('agent')
            ->leftJoin('minute.humans', 'humans')->addSelect('humans')
            ->leftJoin('minute.tribunal', 'tribunal')->addSelect('tribunal')
            ->leftJoin('updating.controls', 'controls')->addSelect('controls')
            ->leftJoin('controls.agent', 'agentControl')->addSelect('agentControl')
            ->leftJoin('controls.agentAttendants', 'agentAttendants')->addSelect('agentAttendants')
            ->leftJoin('controls.humansByMinute', 'humansByMinute')->addSelect('humansByMinute')
            ->leftJoin('controls.humansByControl', 'humansByControl')->addSelect('humansByControl')
            ->leftJoin('controls.editions', 'editions')->addSelect('editions');
    }
}
