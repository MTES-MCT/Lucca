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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\CoreBundle\Repository\AdherentRepository;

/**
 * Class MinuteRepository
 *
 * @package Lucca\Bundle\MinuteBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 */
class MinuteRepository extends EntityRepository
{
    /** Traits */
    use AdherentRepository;

    /*******************************************************************************************/
    /********************* Stats methods *****/
    /*******************************************************************************************/

    /**
     * Stat for overall reports
     * Use on overall Stat
     *
     * @param $p_dateStart
     * @param $p_dateEnd
     * @param null $p_adherent
     * @param null $p_town
     * @param null $p_interco
     * @param null $p_service
     * @param null $p_townAdherent
     * @return int|mixed|string
     */
    public function statMinuteOverall($p_dateStart, $p_dateEnd,
                                      $p_adherent = null, $p_town = null, $p_interco = null, $p_service = null, $p_townAdherent = null)
    {
        $qb = $this->queryMinute();

        $qb->andWhere($qb->expr()->between('minute.dateOpening', ':q_start', ':q_end'))
            ->setParameter(':q_start', $p_dateStart)
            ->setParameter(':q_end', $p_dateEnd);

        $qb->orderBy('minute.dateOpening', 'ASC');

        if ($p_adherent && count($p_adherent) > 0) {
            $qb->andWhere($qb->expr()->in('adherent', ':q_adherent'))
                ->setParameter(':q_adherent', $p_adherent);
        }

        /** Filter on minute location */
        if ($p_town && count($p_town) > 0) {
            $qb->andWhere($qb->expr()->in('plot_town', ':q_townPlot'))
                ->setParameter(':q_townPlot', $p_town);
        }

        /** Filter on adherent data */
        if ($p_townAdherent && count($p_townAdherent) > 0) {
            $qb->andWhere($qb->expr()->in('town', ':q_townAdherent'))
                ->setParameter(':q_townAdherent', $p_townAdherent);
        }

        if ($p_interco && count($p_interco) > 0) {
            $qb->andWhere($qb->expr()->in('intercommunal', ':q_intercommunal'))
                ->setParameter(':q_intercommunal', $p_interco);
        }

        if ($p_service && count($p_service) > 0) {
            $qb->andWhere($qb->expr()->in('service', ':q_service'))
                ->setParameter(':q_service', $p_service);
        }


        $qb->select(array(
            'partial minute.{id, num, dateOpening}',
            'partial plot.{id,  parcel, address}',
            'partial plot_town.{id, name}',
            'partial adherent.{id, name, function}',
            'partial town.{id, name}',
            'partial intercommunal.{id, name}',
            'partial service.{id, name, code}',
            'partial controls.{id}',
            'partial folder.{id}',
        ));

        return $qb->getQuery()->getResult();
    }

    /**
     * Stat for overall reports
     * Use on table Stat
     *
     * @param null $p_dateStart
     * @param null $p_dateEnd
     * @param null $p_adherent
     * @param null $p_town
     * @param null $p_interco
     * @param null $p_service
     * @param null $p_origin
     * @param null $p_risk
     * @param null $p_folders
     * @return int|mixed|string
     */
    public function statMinutes($p_dateStart = null, $p_dateEnd = null,
                                $p_adherent = null, $p_town = null, $p_interco = null, $p_service = null,
                                $p_origin = null, $p_risk = null, $p_townAdherent = null, $p_folders = null)
    {
        $qb = $this->queryMinute();

        if ($p_dateStart != null && $p_dateEnd != null) {
            $qb->andWhere($qb->expr()->between('minute.dateOpening', ':q_start', ':q_end'))
                ->setParameter(':q_start', $p_dateStart)
                ->setParameter(':q_end', $p_dateEnd);
        }

        $qb->orderBy('minute.dateOpening', 'ASC');

        if ($p_adherent != null && count($p_adherent) > 0) {
            $qb->andWhere($qb->expr()->in('adherent', ':q_adherent'))
                ->setParameter(':q_adherent', $p_adherent);
        }

        /** Filter on minute location */
        if ($p_town && count($p_town) > 0) {
            $qb->andWhere($qb->expr()->in('plot_town', ':q_townPlot'))
                ->setParameter(':q_townPlot', $p_town);
        }

        /** Filter on adherent data */
        if ($p_townAdherent && count($p_townAdherent) > 0) {
            $qb->andWhere($qb->expr()->in('town', ':q_townAdherent'))
                ->setParameter(':q_townAdherent', $p_townAdherent);
        }

        if ($p_interco != null && count($p_interco) > 0) {
            $qb->andWhere($qb->expr()->in('intercommunal', ':q_intercommunal'))
                ->setParameter(':q_intercommunal', $p_interco);
        }

        if ($p_service != null && count($p_service) > 0) {
            $qb->andWhere($qb->expr()->in('service', ':q_service'))
                ->setParameter(':q_service', $p_service);
        }

        if ($p_origin != null && count($p_origin) > 0) {
            $qb->andWhere($qb->expr()->in('minute.origin', ':q_origin'))
                ->setParameter(':q_origin', $p_origin);
        }

        if ($p_risk != null && count($p_risk) > 0) {
            $qb->andWhere($qb->expr()->in('plot.risk', ':q_risk'))
                ->setParameter(':q_risk', $p_risk);
        }

        if ($p_folders != null && count($p_folders) > 0) {
            $qb->andWhere($qb->expr()->in('folder', ':q_folder'))
                ->setParameter(':q_folder', $p_folders);
        }

        $qb->select(array(
            'partial minute.{id, num, dateOpening, origin}',
            'partial adherent.{id, name, firstname, service, function}',
            'partial service.{id, name}',
            'partial plot.{id,  parcel, address, isRiskZone, risk}',
            'partial plot_town.{id, name}',
            'partial town.{id, name}',
            'partial controls.{id, folder, stateControl}',
            'partial folder.{id, dateClosure, nature}',
            'partial natinfs.{id, num, qualification}',
        ));

        return $qb->getQuery()->getResult();
    }

    /**
     * Stat for overall reports
     * Use on table Stat
     *
     * @param null $p_adherents
     * @return int|mixed|string
     */
    public function statMinutesByAdherents($p_adherents = null)
    {
        $qb = $this->queryMinute();

        if ($p_adherents != null && count($p_adherents) > 0) {
            $qb->andWhere($qb->expr()->in('adherent', ':q_adherent'))
                ->setParameter(':q_adherent', $p_adherents);
        }

        $qb->select(array(
            'partial minute.{id, num, closure, plot}',
            'partial plot.{id, town}',
            'partial adherent.{id, function, city}',
            'partial town.{id, name, code}',
            'partial plot_town.{id, name, code}',
        ));

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Custom find methods *****/
    /*******************************************************************************************/

    /**
     * Find all minutes between 2 ids to avoid issues when work on huge database
     *
     * @param $p_startId
     * @param $p_endId
     * @return int|mixed|string
     */
    public function findAllBetweenId($p_startId, $p_endId)
    {
        $qb = $this->queryMinuteCommand();

        $qb->andWhere($qb->expr()->between('minute.id', ':q_startId', ':q_endId'))
            ->setParameter('q_startId', $p_startId)
            ->setParameter('q_endId', $p_endId);

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all closed folders with geo code in a specific area and by adherent
     *
     * @param $p_minLat
     * @param $p_maxLat
     * @param $p_minLon
     * @param $p_maxLon
     * @param Adherent|null $p_adherent
     * @param null $p_closed
     * @param null $p_maxResults
     * @param null $p_minutes
     * @return int|mixed|string
     */
    public function findAllInArea($p_minLat, $p_maxLat, $p_minLon, $p_maxLon, Adherent $p_adherent = null, $p_closed = null, $p_maxResults = null, $p_minutes = null)
    {
        $qb = $this->getLocalized($p_adherent, $p_closed);

        $qb->andWhere($qb->expr()->between('plot.latitude', ':q_minLat', ':q_maxLat'))
            ->andWhere($qb->expr()->between('plot.longitude', ':q_minLon', ':q_maxLon'))
            ->setParameter('q_minLat', $p_minLat)
            ->setParameter('q_maxLat', $p_maxLat)
            ->setParameter('q_minLon', $p_minLon)
            ->setParameter('q_maxLon', $p_maxLon);

        if ($p_minutes && count($p_minutes) > 0) {
            $qb->andwhere($qb->expr()->in('minute.id', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        if ($p_maxResults) {
            $qb->groupBy('minute');
            $qb->setMaxResults($p_maxResults);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all minutes with geo code and by adherent
     *
     * @param Adherent|null $p_adherent
     * @param null $p_stateClosed
     * @return mixed
     */
    public function findAllWithGeocodeDashboard(Adherent $p_adherent = null, $p_stateClosed = null)
    {
        $qb = $this->getLocalized($p_adherent, $p_stateClosed);

        $qb->select('minute.num, minute.id, minute.dateComplaint,
        plot.latitude as plotLat, plot.longitude as plotLng, plot.address as plotAddr, plot.place as plotPlace,
        plot.parcel as plotParcel, plot_town.name as plotTownName, plot_town.code as plotTownCode, 
        controls.id as ctrlsId, updatings.id as updatingsId, decisions.id as decisionsId, 
        agent.name as agentName, agent.firstname as agentFirstname');

        $qb->groupBy('minute.num');

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all minutes with geo code and by adherent
     *
     * @param Adherent|null $p_adherent
     * @param null $p_stateClosed
     * @return mixed
     */
    public function findAllSpottingWithGeocode(Adherent $p_adherent = null, $p_stateClosed = null)
    {
        $qb = $this->getLocalized($p_adherent, $p_stateClosed);

        $qb->andWhere($qb->expr()->eq('SIZE(minute.controls)', 0))
            ->andWhere($qb->expr()->eq('SIZE(minute.updatings)', 0))
            ->andWhere($qb->expr()->eq('SIZE(minute.decisions)', 0));

        return $qb->getQuery()->getResult();
    }

    /**
     * Find Minutes with browser's filters.
     * Used on Minute browser view
     *
     * @param Adherent|null $adherent
     * @param null $p_fdateStart
     * @param null $p_fdateEnd
     * @param null $p_fnum
     * @param null $p_fstatus
     * @param null $p_fadherent
     * @param null $p_ftown
     * @param null $p_finterco
     * @param null $p_aservice
     * @param null $p_atown
     * @param null $p_ainterco
     * @return mixed
     */
    public function findMinutesBrowser(
        Adherent $adherent = null, $p_fdateStart = null, $p_fdateEnd = null, $p_fnum = null, $p_fstatus = null,
                 $p_fadherent = null, $p_ftown = null, $p_finterco = null, $p_aservice = null, $p_atown = null, $p_ainterco = null
    )
    {
        $qb = $this->queryMinuteBrowser();

        $qb->andWhere($qb->expr()->between('minute.dateOpening', ':q_start', ':q_end'))
            ->setParameter(':q_start', $p_fdateStart)
            ->setParameter(':q_end', $p_fdateEnd);

        $qb->orderBy('minute.dateOpening', 'ASC');

        if ($p_fnum) {
            $qb->andWhere($qb->expr()->like('minute.num', ':q_num'))
                ->setParameter(':q_num', '%' . $p_fnum . '%');
        }

        if ($p_fstatus && count($p_fstatus) > 0) {
            $qb->andWhere($qb->expr()->in('minute.status', ':q_status'))
                ->setParameter(':q_status', $p_fstatus);
        }

        /** use xor to enter only if have $p_ftown or $p_finterco but not both */
        if ($p_ftown && count($p_ftown) > 0 xor $p_finterco && count($p_finterco) > 0) {

            if ($p_ftown && count($p_ftown) > 0) {
                $qb->andWhere(
                    $qb->expr()->in('plot_town', ':q_folder_town'))
                    ->setParameter(':q_folder_town', $p_ftown);
            }

            if ($p_finterco && count($p_finterco) > 0) {
                $qb->andWhere($qb->expr()->in('plot_intercommunal', ':q_folder_intercommunal'))
                    ->setParameter(':q_folder_intercommunal', $p_finterco);
            }
        } else if ($p_ftown && count($p_ftown) > 0 && $p_finterco && count($p_finterco) > 0) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('plot_intercommunal', ':q_folder_intercommunal'),
                    $qb->expr()->in('plot_town', ':q_folder_town')
                ))
                ->setParameter(':q_folder_town', $p_ftown)
                ->setParameter(':q_folder_intercommunal', $p_finterco);
        }

        if ($adherent) {
            // Call Trait
            $qb = $this->getValuesAdherent($adherent, $qb);
        } else {
            if ($p_fadherent && count($p_fadherent) > 0) {
                $qb->andWhere($qb->expr()->in('adherent', ':q_adherent'))
                    ->setParameter(':q_adherent', $p_fadherent);
            }

            /** use xor to enter only if have $p_atown or $p_ainterco but not both */
            if ($p_atown && count($p_atown) > 0 xor $p_ainterco && count($p_ainterco) > 0) {
                if ($p_atown && count($p_atown) > 0) {
                    $qb->andWhere(
                        $qb->expr()->in('town', ':q_adherent_town')
                    )
                        ->setParameter(':q_adherent_town', $p_atown);
                }

                if ($p_ainterco && count($p_ainterco) > 0) {
                    $qb->andWhere($qb->expr()->in('intercommunal', ':q_adherent_intercommunal'))
                        ->setParameter(':q_adherent_intercommunal', $p_ainterco);
                }
            } else if ($p_atown && count($p_atown) > 0 && $p_ainterco && count($p_ainterco) > 0) {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->in('intercommunal', ':q_adherent_intercommunal'),
                        $qb->expr()->in('town', ':q_adherent_town')
                    ))
                    ->setParameter(':q_adherent_town', $p_atown)
                    ->setParameter(':q_adherent_intercommunal', $p_ainterco);
            }

            if ($p_aservice && count($p_aservice) > 0) {
                $qb->andWhere($qb->expr()->in('service', ':q_service'))
                    ->setParameter(':q_service', $p_aservice);
            }

        }

        $qb->select([
            'partial user.{id, username}',
            'partial adherent.{id, name}',
            'partial minute.{id, num, dateOpening}',
            'partial plot.{id, parcel, latitude, longitude}',
            'partial plot_town.{id, name}',
            'partial plot_intercommunal.{id}',
            'partial humans.{id, name, firstname}',
            'partial closure.{id, dateClosing}',
            'partial decisions.{id}',
            'partial tribunalCommission.{id, dateJudicialDesision, statusDecision}',
            'partial appealCommission.{id, dateJudicialDesision, statusDecision}',
            'partial controls2.{id}',
            'partial folder3.{id, type, dateClosure}',
            'partial tagsNature3.{id, name}',
            'partial updatings.{id}',
            'partial controls62.{id}',
            'partial folder63.{id, dateClosure, num}',
            'partial service.{id}',
            'partial town.{id, name}',
            'partial intercommunal.{id}',
        ]);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find a minute entity with Control entity
     * Used on Test Controller
     *
     * @param Control $p_control
     * @return mixed
     */
    public function findMinuteByControl(Control $p_control)
    {
        $qb = $this->queryMinute();

        $qb->where($qb->expr()->in('controls', ':q_control'))
            ->setParameter(':q_control', $p_control);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Minute Repository - ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Find max num used for 1 year
     * Use on Code generator
     *
     * @param $year
     * @return mixed
     */
    public function findMaxNumForYear($year)
    {
        $qb = $this->createQueryBuilder('minute');

        $qb->where($qb->expr()->like('minute.num', ':num'))
            ->setParameter('num', '%' . $year . '%');

        $qb->select($qb->expr()->max('minute.num'));

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Minute Repository - ' . $e->getMessage();
            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Get specifics queries *****/
    /*******************************************************************************************/
    /**
     * Get folder with geo code and with closure date and by adherent and by state (open or closed)
     *
     * @param Adherent|null $p_adherent
     * @param null $p_stateClosed
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getLocalized(Adherent $p_adherent = null, $p_stateClosed = null)
    {
        $qb = $this->queryMinute();

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

        if ($p_stateClosed != null && count($p_stateClosed) > 0)
            $qb->andWhere($qb->expr()->in('closure.status', ':q_state'))
                ->setParameter('q_state', $p_stateClosed);
        else
            $qb->andWhere($qb->expr()->isNull('closure'));

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Minute dependencies
     *
     * @return array
     */
    public function findAll()
    {
        $qb = $this->queryMinuteBrowser();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Minute dependencies
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return bool|mixed|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $qb = $this->queryMinuteShow();

        $qb->where($qb->expr()->eq('minute.id', ':q_minute'))
            ->setParameter(':q_minute', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Minute Repository - ' . $e->getMessage();
            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Minute Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryMinute()
    {
        $qb = $this->createQueryBuilder('minute')
            ->leftJoin('minute.closure', 'closure')->addSelect('closure')
            ->leftJoin('minute.controls', 'controls')->addSelect('controls')
            ->leftJoin('controls.folder', 'folder')->addSelect('folder')
            ->leftJoin('folder.tagsNature', 'tagsNature')->addSelect('tagsNature')
            ->leftJoin('folder.tagsTown', 'tagsTown')->addSelect('tagsTown')
            ->leftJoin('folder.natinfs', 'natinfs')->addSelect('natinfs')
            ->leftJoin('minute.plot', 'plot')->addSelect('plot')
            ->leftJoin('plot.town', 'plot_town')->addSelect('plot_town')
            ->leftJoin('plot_town.intercommunal', 'plot_intercommunal')->addSelect('plot_intercommunal')
            ->leftJoin('minute.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('adherent.user', 'user')
            ->leftJoin('adherent.town', 'town')
            ->leftJoin('adherent.intercommunal', 'intercommunal')
            ->leftJoin('adherent.service', 'service')
            ->leftJoin('minute.agent', 'agent')->addSelect('agent')
            ->leftJoin('minute.humans', 'humans')->addSelect('humans')
            ->leftJoin('minute.tribunal', 'tribunal')->addSelect('tribunal')
            ->leftJoin('minute.updatings', 'updatings')->addSelect('updatings')
            ->leftJoin('minute.decisions', 'decisions')->addSelect('decisions');

        return $qb;
    }

    /**
     * Query for command
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryMinuteCommand()
    {
        $qb = $this->createQueryBuilder('minute')
            ->leftJoin('minute.closure', 'closure')->addSelect('closure')
            ->leftJoin('minute.controls', 'controls')->addSelect('controls')
            ->leftJoin('controls.folder', 'folder')->addSelect('folder')
            ->leftJoin('minute.updatings', 'updatings')->addSelect('updatings')
            ->leftJoin('minute.decisions', 'decisions')->addSelect('decisions');

        return $qb;
    }

    /**
     * All dependencies to display one Minute Entity
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryMinuteShow()
    {
        $qb = $this->createQueryBuilder('minute')
            ->leftJoin('minute.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('adherent.user', 'user')
            ->leftJoin('adherent.town', 'town')
            ->leftJoin('adherent.intercommunal', 'intercommunal')
            ->leftJoin('adherent.service', 'service');

        $qb->leftJoin('minute.plot', 'plot')->addSelect('plot')
            ->leftJoin('plot.town', 'plot_town')->addSelect('plot_town');

        $qb->leftJoin('minute.agent', 'agent')->addSelect('agent')
            ->leftJoin('minute.humans', 'humans')->addSelect('humans')
            ->leftJoin('minute.tribunal', 'tribunal')->addSelect('tribunal')
            ->leftJoin('minute.closure', 'closure')->addSelect('closure');

        $qb->leftJoin('minute.decisions', 'decisions')->addSelect('decisions')
            ->leftJoin('decisions.tribunal', 'tribunalDeci')->addSelect('tribunalDeci')
            ->leftJoin('decisions.tribunalCommission', 'tribunalCommission')->addSelect('tribunalCommission')
            ->leftJoin('decisions.appealCommission', 'appealCommission')->addSelect('appealCommission');

        $qb->leftJoin('minute.controls', 'controls2')->addSelect('controls2')
            ->leftJoin('controls2.agent', 'agentControl2')->addSelect('agentControl2')
            ->leftJoin('controls2.agentAttendants', 'agentAttendants2')->addSelect('agentAttendants2')
            ->leftJoin('controls2.humansByMinute', 'humansByMinute2')->addSelect('humansByMinute2')
            ->leftJoin('controls2.humansByControl', 'humansByControl2')->addSelect('humansByControl2')
            ->leftJoin('controls2.folder', 'folder3')->addSelect('folder3');

        $qb->leftJoin('minute.updatings', 'updatings')->addSelect('updatings')
            ->leftJoin('updatings.controls', 'controls62')->addSelect('controls62')
            ->leftJoin('controls62.agent', 'agentControl62')->addSelect('agentControl62')
            ->leftJoin('controls62.agentAttendants', 'agentAttendants62')->addSelect('agentAttendants62')
            ->leftJoin('controls62.humansByMinute', 'humansByMinute62')->addSelect('humansByMinute62')
            ->leftJoin('controls62.humansByControl', 'humansByControl62')->addSelect('humansByControl62')
            ->leftJoin('controls62.folder', 'folder63')->addSelect('folder63');

        return $qb;
    }

    /**
     * All dependencies to display one Minute Entity
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryMinuteBrowser()
    {
        $qb = $this->createQueryBuilder('minute')
            ->leftJoin('minute.adherent', 'adherent')
            ->leftJoin('adherent.town', 'town')
            ->leftJoin('adherent.intercommunal', 'intercommunal')
            ->leftJoin('adherent.service', 'service')
            ->leftJoin('adherent.user', 'user');

        $qb->leftJoin('minute.plot', 'plot')
            ->leftJoin('plot.town', 'plot_town')
            ->leftJoin('plot_town.intercommunal', 'plot_intercommunal');

        $qb->leftJoin('minute.humans', 'humans')
            ->leftJoin('minute.closure', 'closure');

        $qb->leftJoin('minute.decisions', 'decisions')
            ->leftJoin('decisions.tribunalCommission', 'tribunalCommission')
            ->leftJoin('decisions.appealCommission', 'appealCommission')
            ->leftJoin('decisions.expulsion', 'expulsion')
            ->leftJoin('decisions.demolition', 'demolition');

        $qb->leftJoin('minute.controls', 'controls2')
            ->leftJoin('controls2.folder', 'folder3');


        $qb->leftJoin('folder3.tagsTown', 'tagsTown3')
            ->leftJoin('folder3.tagsNature', 'tagsNature3');

        $qb->leftJoin('minute.updatings', 'updatings')
            ->leftJoin('updatings.controls', 'controls62')
            ->leftJoin('controls62.folder', 'folder63');

        $qb->leftJoin('folder63.tagsTown', 'tagsTown63')
            ->leftJoin('folder63.tagsNature', 'tagsNature63');

        return $qb;
    }
}

