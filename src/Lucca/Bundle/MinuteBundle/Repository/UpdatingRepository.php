<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Repository;

use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;

/**
 * Class UpdatingRepository
 *
 * @package Lucca\Bundle\MinuteBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 */
class UpdatingRepository extends EntityRepository
{
    /**
     * Method used to find all updating with geo code in a specific area and by adherent
     *
     * @param $p_minLat
     * @param $p_maxLat
     * @param $p_minLon
     * @param $p_maxLon
     * @param Adherent|null $p_adherent
     * @param null $p_maxResults
     * @param null $p_minutes
     * @return int|mixed|string
     */
    public function findAllInArea($p_minLat, $p_maxLat, $p_minLon, $p_maxLon, Adherent $p_adherent = null, $p_maxResults = null, $p_minutes = null)
    {
        $qb = $this->getLocalized($p_adherent);

        $qb->andWhere($qb->expr()->between('plot.latitude', ':q_minLat', ':q_maxLat'))
            ->andWhere($qb->expr()->between('plot.longitude', ':q_minLon', ':q_maxLon'))
            ->setParameter('q_minLat', $p_minLat)
            ->setParameter('q_maxLat', $p_maxLat)
            ->setParameter('q_minLon', $p_minLon)
            ->setParameter('q_maxLon', $p_maxLon);

        if ($p_minutes && count($p_minutes) > 0) {
            $qb->andwhere($qb->expr()->in('updating.minute', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        if($p_maxResults){
            $qb->groupBy('updating');
            $qb->setMaxResults($p_maxResults);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all updating with geo code and by adherent
     *
     * @param Adherent|null $p_adherent
     * @return mixed
     */
    public function findAllWithGeocodeDashboard(Adherent $p_adherent = null)
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
     *
     * @param Adherent|null $p_adherent
     * @return mixed
     */
    public function findAllWithGeocode(Adherent $p_adherent = null)
    {
        $qb = $this->getLocalized($p_adherent);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find updating entity by Control
     * used for Folder document
     *
     * @param Control $control
     * @return bool|mixed
     */
    public function findUpdatingByControl(Control $control)
    {
        $qb = $this->queryUpdating();

        $qb->where($qb->expr()->in('controls', ':control'))
            ->setParameter(':control', $control);

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
     *
     * @param Minute $minute
     * @return array
     */
    public function findByMinute(Minute $minute)
    {
        $qb = $this->queryUpdating();

        $qb->where($qb->expr()->eq('updating.minute', ':minute'))
            ->setParameter(':minute', $minute);

        $qb->orderBy('updating.num', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find max num used for 1 minute
     * Use on Code generator
     *
     * @param $prefix
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findMaxNumForMinute($prefix)
    {
        $qb = $this->createQueryBuilder('updating');

        $qb->where($qb->expr()->like('updating.num', ':num'))
            ->setParameter('num', '%' . $prefix . '%');

        $qb->select($qb->expr()->max('updating.num'));

        return $qb->getQuery()->getOneOrNullResult();
    }

    /*******************************************************************************************/
    /********************* Get specifics queries *****/
    /*******************************************************************************************/
    /**
     * Get updating with geo code and with closure date and by adherent
     *
     * @param Adherent|null $p_adherent
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getLocalized(Adherent $p_adherent = null)
    {
        $qb = $this->queryUpdating();

        $qb->where($qb->expr()->isNotNull('plot.latitude'))
            ->andWhere($qb->expr()->isNotNull('plot.longitude'));

        if ($p_adherent) {
            if ($p_adherent->getIntercommunal())
                $qb->andWhere($qb->expr()->eq('plot_intercommunal', ':q_intercommunal'))
                    ->setParameter('q_intercommunal', $p_adherent->getIntercommunal());

            elseif ($p_adherent->getTown())
                $qb->andWhere($qb->expr()->eq('plot_town', ':q_town'))
                    ->setParameter(':q_town', $p_adherent->getTown());
        }

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Updating dependencies
     *
     * @return array
     */
    public function findAll()
    {
        $qb = $this->queryUpdating();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Updating dependencies
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return bool|mixed|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $qb = $this->queryUpdating();

        $qb->where($qb->expr()->eq('updating.id', ':q_updating'))
            ->setParameter(':q_updating', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Updating Repository - ' . $e->getMessage();
            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Updating Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryUpdating()
    {
        $qb = $this->createQueryBuilder('updating')
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

        return $qb;
    }
}
