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

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Lucca\AdherentBundle\Entity\Adherent;

/**
 * Class ControlRepository
 *
 * @package Lucca\MinuteBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 */
class ControlRepository extends EntityRepository
{
    /*******************************************************************************************/
    /********************* Stats methods *****/
    /*******************************************************************************************/
    /**
     * Count type of decision between date of minute opening
     *
     * @param null $p_minutes
     * @return int|mixed|string
     */
    public function findBetweenDates($p_minutes = null, $p_state = [Control::STATE_INSIDE, Control::STATE_INSIDE_WITHOUT_CONVOCATION])
    {
        $qb = $this->queryControlSimple();

        /************** Filters on minute to get ******************/
        if ($p_minutes && count($p_minutes) > 0) {
            $qb->andWhere($qb->expr()->in('minute', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        if ($p_state && count($p_state) > 0) {
            $qb->andWhere($qb->expr()->in('control.stateControl', ':q_state'))
                ->setParameter(':q_state', $p_state);
        }

        $qb->select(array(
            'partial control.{id}',
            'partial minute.{id, dateOpening}',
        ));

        return $qb->getQuery()->getResult();
    }

    /**
     * Stat for overall minutes reports
     *
     * @param null $p_stateControl
     * @return int|mixed|string
     */
    public function statControl($p_stateControl = null)
    {
        $qb = $this->queryControlSimple();

        if ($p_stateControl != null && count($p_stateControl) > 0) {
            $qb->andWhere($qb->expr()->in('control.stateControl', ':q_stateControl'))
                ->setParameter(':q_stateControl', $p_stateControl);
        }

        $qb->select(array(
            'partial control.{id, folder, stateControl}',
        ));

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Custom findAll methods *****/
    /*******************************************************************************************/
    /**
     * Method used to find all closed folders with geo code in a specific area
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
            $qb->andwhere($qb->expr()->in('control.minute', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        if ($p_maxResults) {
            $qb->groupBy('control');
            $qb->setMaxResults($p_maxResults);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all minutes with geo code
     *
     * @param Adherent|null $p_adherent
     * @return array
     */
    public function findAllWithGeocodeDashboard(Adherent $p_adherent = null)
    {
        $qb = $this->getLocalized($p_adherent);

        $qb->select([
            'partial control.{id, dateControl}',
            'partial minute.{id, num}',
            'partial humansByControl.{id, name, firstname}',
            'partial humansByMinute.{id,name,firstname}',
            'partial plot.{id,latitude,longitude, address, place, parcel}',
            'partial plot_town.{id,name,code}',
            'partial agent.{id,name,firstname}',
        ]);

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all minutes with geo code
     *
     * @param Adherent|null $p_adherent
     * @return array
     */
    public function findAllWithGeocode(Adherent $p_adherent = null)
    {
        $qb = $this->getLocalized($p_adherent);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find Control linked to a Minute entity
     *
     * @param Minute $minute
     * @return mixed
     */
    public function findByMinute(Minute $minute)
    {
        $qb = $this->queryControl();

        $qb->where($qb->expr()->eq('minute', ':q_minute'))
            ->setParameter(':q_minute', $minute);

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Custom find for test methods *****/
    /*******************************************************************************************/
    /**
     * Override findAll method
     * with Courier dependencies
     *
     * @param $p_type
     * @return int|mixed|string
     */
    public function findOneForTest($p_type)
    {
        $qb = $this->queryControl();

        $qb->where($qb->expr()->isNull('adherent.logo'));

        $qb->andWhere($qb->expr()->eq('control.type', ':q_type'))
            ->setParameter(':q_type', $p_type);

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
     * with Control dependencies
     *
     * @return array
     */
    public function findAll()
    {
        $qb = $this->queryControl();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Control dependencies
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return bool|mixed|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $qb = $this->queryControl();

        $qb->where($qb->expr()->eq('control.id', ':q_control'))
            ->setParameter(':q_control', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Control Repository - ' . $e->getMessage();
            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Get specifics queries *****/
    /*******************************************************************************************/
    /**
     * Get folder with geo code and with closure date and by adherent
     *
     * @param Adherent|null $p_adherent
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getLocalized(Adherent $p_adherent = null)
    {
        $qb = $this->queryControl();

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
    /********************* Query - Dependencies of Control Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryControlSimple()
    {
        $qb = $this->createQueryBuilder('control')
            ->leftJoin('control.minute', 'minute')->addSelect('minute');

        return $qb;
    }

    /**
     * Classic dependencies
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryControl()
    {
        $qb = $this->createQueryBuilder('control')
            ->leftJoin('control.minute', 'minute')->addSelect('minute')
            ->leftJoin('minute.plot', 'plot')->addSelect('plot')
            ->leftJoin('plot.town', 'plot_town')->addSelect('plot_town')
            ->leftJoin('plot_town.intercommunal', 'plot_intercommunal')->addSelect('plot_intercommunal')
            ->leftJoin('minute.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('control.humansByMinute', 'humansByMinute')->addSelect('humansByMinute')
            ->leftJoin('control.humansByControl', 'humansByControl')->addSelect('humansByControl')
            ->leftJoin('control.agent', 'agent')->addSelect('agent')
            ->leftJoin('control.agentAttendants', 'agentAttendants')->addSelect('agentAttendants')
            ->leftJoin('control.editions', 'editions')->addSelect('editions')
            ->leftJoin('control.folder', 'folder')->addSelect('folder');

        return $qb;
    }
}
