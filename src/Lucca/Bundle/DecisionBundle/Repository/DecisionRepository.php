<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Repository;

use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;

/**
 * Class DecisionRepository
 *
 * @package Lucca\Bundle\DecisionBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class DecisionRepository extends EntityRepository
{
    /*******************************************************************************************/
    /********************* Stats methods *****/
    /*******************************************************************************************/
    /**
     * Get all decisions between date on minute
     *
     * @param null $p_minutes
     * @return int|mixed|string
     */
    public function findBetweenDates($p_minutes = null): mixed
    {
        $qb = $this->queryDecisionSimple();

        /************** Filters on minute to get ******************/
        if ($p_minutes && count($p_minutes) > 0) {
            $qb->andWhere($qb->expr()->in('minute', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        $qb->select(array(
            'partial decision.{id}',
            'partial minute.{id, dateOpening}',
            'partial expulsion.{id}',
            'partial demolition.{id}',
        ));

        return $qb->getQuery()->getResult();
    }

    /**
     * Count type of decision between date of minute opening
     *
     * @param null $p_minutes
     * @return int|mixed|string
     */
    public function countTypesBetweenDates($p_minutes = null): mixed
    {
        $qb = $this->queryDecision();

        /************** Filters on minute to get ******************/
        if ($p_minutes && count($p_minutes) > 0) {
            $qb->andWhere($qb->expr()->in('minute', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        $qb->addGroupBy('tribunalCommission.statusDecision');

        $qb->select(array(
            'COUNT(tribunalCommission.statusDecision) as countType',
            'tribunalCommission.statusDecision',
        ));

        return $qb->getQuery()->getResult();
    }

    /**
     * Count type of decision between date of minute opening
     *
     * @param null $p_minutes
     * @return int|mixed|string
     */
    public function countBetweenDates($p_minutes = null): mixed
    {
        $qb = $this->queryDecision();

        /************** Filters on minute to get ******************/
        if ($p_minutes && count($p_minutes) > 0) {
            $qb->andWhere($qb->expr()->in('minute', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        /** Be careful the names 'count' defined here are used multiple times in the app */
        $qb->select(array(
            'COUNT(demolition) as countDemolition',
            'COUNT(penalties) as countPenalties',
            'COUNT(contradictories) as countContradictories',
            'COUNT(expulsion) as countExpulsion',
        ));

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Custom find methods *****/
    /*******************************************************************************************/

    /**
     * Method used to find all decision with geo code in a specific area and by adherent
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
    public function findAllInArea($p_minLat, $p_maxLat, $p_minLon, $p_maxLon, Adherent $p_adherent = null, $p_maxResults = null, $p_minutes = null): mixed
    {
        $qb = $this->getLocalizedByAdherent($p_adherent);

        $qb->andWhere($qb->expr()->between('plot.latitude', ':q_minLat', ':q_maxLat'))
            ->andWhere($qb->expr()->between('plot.longitude', ':q_minLon', ':q_maxLon'))
            ->setParameter('q_minLat', $p_minLat)
            ->setParameter('q_maxLat', $p_maxLat)
            ->setParameter('q_minLon', $p_minLon)
            ->setParameter('q_maxLon', $p_maxLon);

        if ($p_minutes && count($p_minutes) > 0) {
            $qb->andwhere($qb->expr()->in('decision.minute', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        if ($p_maxResults) {
            $qb->groupBy('decision');
            $qb->setMaxResults($p_maxResults);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all decision with geo code and by adherent
     *
     * @param Adherent|null $p_adherent
     * @return array
     */
    public function findAllWithGeocodeDashboard(Adherent $p_adherent = null): array
    {
        $qb = $this->getLocalizedByAdherent($p_adherent);

        $qb->select('decision.id, decision.appeal, decision.amountPenaltyDaily,
        minute.id as minuteId, minute.num as minuteNum, 
        plot.latitude as plotLat, plot.longitude as plotLng,plot.address as plotAddr, plot.place as plotPlace, 
        plot.parcel as plotParcel, plot_town.name as plotTownName, plot_town.code as plotTownCode,
        tribunalCommission.dateHearing as TCdateHearing, tribunalCommission.statusDecision as TCstatusDecision, 
        penalties.dateFolder as penaltiesDate, penalties.nature as penaltiesNature');

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all decision with geo code and by adherent
     *
     * @param Adherent|null $p_adherent
     * @return array
     */
    public function findAllWithGeocode(Adherent $p_adherent = null): array
    {
        $qb = $this->getLocalizedByAdherent($p_adherent);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all decision by a Minute entity
     *
     * @param Minute $p_minute
     * @return array
     */
    public function findDecisionsByMinute(Minute $p_minute): array
    {
        $qb = $this->queryDecision();

        $qb->where($qb->expr()->eq('decision.minute', ':q_minute'))
            ->setParameter(':q_minute', $p_minute);

        $qb->orderBy('decision.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Filters *****/
    /*******************************************************************************************/
    /**
     * Get decision with geo code and by adherent
     *
     * @param Adherent|null $p_adherent
     * @return QueryBuilder
     */
    private function getLocalizedByAdherent(Adherent $p_adherent = null): QueryBuilder
    {
        $qb = $this->queryDecision();

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
     * with Decision dependencies
     *
     * @return array
     */
    public function findAll(): array
    {
        $qb = $this->queryDecision();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Decision dependencies
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return bool|mixed|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null): mixed
    {
        $qb = $this->queryDecision();

        $qb->where($qb->expr()->eq('decision.id', ':q_decision'))
            ->setParameter(':q_decision', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Decision Repository - ' . $e->getMessage();
            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Decision Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     *
     * @return QueryBuilder
     */
    private function queryDecisionSimple(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('decision')
            ->leftJoin('decision.minute', 'minute')->addSelect('minute')
            ->leftJoin('decision.expulsion', 'expulsion')->addSelect('expulsion')
            ->leftJoin('decision.demolition', 'demolition')->addSelect('demolition');

        return $qb;
    }

    /**
     * Classic dependencies
     *
     * @return QueryBuilder
     */
    private function queryDecision(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('decision')
            ->leftJoin('decision.minute', 'minute')->addSelect('minute')
            ->leftJoin('minute.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('minute.plot', 'plot')->addSelect('plot')
            ->leftJoin('plot.town', 'plot_town')->addSelect('plot_town')
            ->leftJoin('plot_town.intercommunal', 'plot_intercommunal')->addSelect('plot_intercommunal')
            ->leftJoin('decision.tribunal', 'tribunal')->addSelect('tribunal')
            ->leftJoin('decision.tribunalCommission', 'tribunalCommission')->addSelect('tribunalCommission')
            ->leftJoin('decision.appealCommission', 'appealCommission')->addSelect('appealCommission')
            ->leftJoin('decision.cassationComission', 'cassationComission')->addSelect('cassationComission')
            ->leftJoin('decision.penalties', 'penalties')->addSelect('penalties')
            ->leftJoin('decision.liquidations', 'liquidations')->addSelect('liquidations')
            ->leftJoin('decision.appealPenalties', 'appealPenalties')->addSelect('appealPenalties')
            ->leftJoin('decision.contradictories', 'contradictories')->addSelect('contradictories')
            ->leftJoin('decision.expulsion', 'expulsion')->addSelect('expulsion')
            ->leftJoin('decision.demolition', 'demolition')->addSelect('demolition');

        return $qb;
    }
}
