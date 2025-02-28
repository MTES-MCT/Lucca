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
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Lucca\AdherentBundle\Entity\Adherent;
use Lucca\CoreBundle\Repository\AdherentRepository;

/**
 * Class FolderRepository
 *
 * @package Lucca\MinuteBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 */
class FolderRepository extends EntityRepository
{
    /** Traits */
    use AdherentRepository;

    /*******************************************************************************************/
    /********************* Stats methods *****/
    /*******************************************************************************************/
    /**
     * Count type of decision between date of minute opening
     *
     * @param null $p_minutes
     * @return int|mixed|string
     */
    public function findBetweenDates($p_minutes = null)
    {
        $qb = $this->queryFolderSimple();

        /************** Filters on minute to get ******************/
        if ($p_minutes && count($p_minutes) > 0) {
            $qb->andWhere($qb->expr()->in('minute', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        $qb->andWhere($qb->expr()->isNotNull('folder.courier'));

        $qb->select(array(
            'partial folder.{id}',
            'partial natinfs.{id}',
            'partial minute.{id, dateOpening}',
        ));

        return $qb->getQuery()->getResult();
    }

    /**
     * Stat for overall minutes reports
     *
     * @param null $p_natinf
     * @return int|mixed|string
     */
    public function statFolders($p_natinf = null, $p_nature = null, $p_dateStartClosure = null, $p_dateEndClosure = null, $p_controls = null)
    {
        $qb = $this->queryFolder();

        if ($p_natinf !== null && count($p_natinf) > 0) {
            $qb->andWhere($qb->expr()->in('natinfs', ':q_natinf'))
                ->setParameter(':q_natinf', $p_natinf);
        }

        if ($p_nature !== null && count($p_nature) > 0) {
            $qb->andWhere($qb->expr()->in('folder.nature', ':q_nature'))
                ->setParameter(':q_nature', $p_nature);
        }

        if ($p_dateStartClosure !== null && $p_dateEndClosure !== null) {
            $qb->andWhere($qb->expr()->between('folder.dateClosure', ':q_startClosure', ':q_endClosure'))
                ->setParameter(':q_startClosure', $p_dateStartClosure)
                ->setParameter(':q_endClosure', $p_dateEndClosure);
        }

        if ($p_controls != null && count($p_controls) > 0) {
            $qb->andWhere($qb->expr()->in('folder.control', ':q_control'))
                ->setParameter(':q_control', $p_controls);
        }

        $qb->select(array(
            'partial folder.{id, dateClosure, nature}',
        ));

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Custom find methods *****/
    /*******************************************************************************************/
    /**
     * Method used to find all open folders with geo code
     *
     * @param Adherent|null $p_adherent
     * @return array
     */
    public function findAllWithGeocodeDashboard(Adherent $p_adherent = null)
    {
        $qb = $this->filterByAdherentAndStateLocalized($p_adherent);

        $qb->select(array(
            'partial folder.{id, num, nature, dateClosure}',
            'partial control.{id, dateControl}',
            'partial tagsNature.{id, name}',
            'partial tagsTown.{id, name}',
            'partial natinfs.{id, num, qualification}',
            'partial minute.{id, num}',
            'partial plot.{id, latitude, longitude, address, place, parcel}',
            'partial plot_town.{id, name, code}',
        ));

        return $qb->getQuery()->getResult();
    }

    /**
     * Find Folder by Town
     *
     * @param $p_town
     * @param Adherent|null $p_adherent
     * @return mixed
     */
    public function findByTown($p_town, Adherent $p_adherent = null)
    {
//        $qb = $this->filterByAdherentAndStateLocalized($p_adherent);
        $qb = $this->queryFolder();

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->in('town', ':q_town'),
                $qb->expr()->in('plot_town', ':q_town')
            ))
            ->setParameter(':q_town', $p_town);

        $qb->andWhere($qb->expr()->isNotNull('folder.dateClosure'));

        $qb->select(array(
            'partial folder.{id, num, nature, dateClosure}',
            'partial control.{id, dateControl}',
            'partial tagsNature.{id, name}',
            'partial tagsTown.{id, name}',
            'partial natinfs.{id, num, qualification}',
            'partial minute.{id, num}',
            'partial plot.{id, latitude, longitude, address, place, parcel}',
            'partial plot_town.{id, name, code}',
        ));

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Invoice dependencies
     *
     * @param array $ids
     * @param string|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return bool|mixed|object|null
     */
    public function findByIds(array $ids, string $orderBy = null, int $limit = null, int $offset = null)
    {
        $qb = $this->queryFolder();

        $qb->where($qb->expr()->in('folder.id', ':q_folder'))
            ->setParameter(':q_folder', $ids);

        if ($orderBy)
            $qb->orderBy($orderBy);

        if ($limit)
            $qb->setMaxResults($limit);

        if ($offset)
            $qb->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all open folders with geo code
     *
     * @param Adherent|null $p_adherent
     * @return array
     */
    public function findAllWithGeocode(Adherent $p_adherent = null)
    {
        $qb = $this->filterByAdherentAndStateLocalized($p_adherent);

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all open folders with geo code in a specific area
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
        $qb = $this->filterByAdherentAndStateLocalized($p_adherent);

        $qb->andWhere($qb->expr()->between('plot.latitude', ':q_minLat', ':q_maxLat'))
            ->andWhere($qb->expr()->between('plot.longitude', ':q_minLon', ':q_maxLon'))
            ->setParameter('q_minLat', $p_minLat)
            ->setParameter('q_maxLat', $p_maxLat)
            ->setParameter('q_minLon', $p_minLon)
            ->setParameter('q_maxLon', $p_maxLon);

        if ($p_minutes && count($p_minutes) > 0) {
            $qb->andwhere($qb->expr()->in('folder.minute', ':q_minutes'))
                ->setParameter(':q_minutes', $p_minutes);
        }

        if ($p_maxResults) {
            $qb->groupBy('folder');
            $qb->setMaxResults($p_maxResults);
        }

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
     * @param null $p_fnumFolder
     * @param null $p_fadherent
     * @param null $p_ftown
     * @param null $p_finterco
     * @param null $p_fservice
     * @return mixed
     */
    public function findFolderBrowser(Adherent $adherent = null,
                                               $p_fdateStart = null, $p_fdateEnd = null, $p_fnum = null, $p_fnumFolder = null, $p_fadherent = null, $p_ftown = null, $p_finterco = null, $p_fservice = null)
    {
        $qb = $this->queryFolderBrowser();

        $qb->andWhere($qb->expr()->between('minute.dateOpening', ':q_start', ':q_end'))
            ->setParameter(':q_start', $p_fdateStart)
            ->setParameter(':q_end', $p_fdateEnd);

        $qb->orderBy('minute.dateOpening', 'ASC')
            ->addGroupBy('folder');

        if ($p_fnum) {
            $qb->andWhere($qb->expr()->like('minute.num', ':q_num'))
                ->setParameter(':q_num', '%' . $p_fnum . '%');
        }

        if ($p_fnumFolder) {
            $qb->andWhere($qb->expr()->like('folder.num', ':q_num'))
                ->setParameter(':q_num', '%' . $p_fnumFolder . '%');
        }

        if ($adherent) {
            // Call Trait
            $qb = $this->getValuesAdherent($adherent, $qb);
        } else {
            if ($p_fadherent && count($p_fadherent) > 0) {
                $qb->andWhere($qb->expr()->in('adherent', ':q_adherent'))
                    ->setParameter(':q_adherent', $p_fadherent)
                    ->addGroupBy('adherent');
            }

            if ($p_ftown && count($p_ftown) > 0) {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->in('town', ':q_town'),
                        $qb->expr()->in('plot_town', ':q_town')
                    ))
                    ->setParameter(':q_town', $p_ftown)
                    ->addGroupBy('town.name, plot_town.name');
            }

            if ($p_finterco && count($p_finterco) > 0) {
                $qb->andWhere($qb->expr()->in('intercommunal', ':q_intercommunal'))
                    ->setParameter(':q_intercommunal', $p_finterco)
                    ->addGroupBy('intercommunal');
            }

            if ($p_fservice && count($p_fservice) > 0) {
                $qb->andWhere($qb->expr()->in('service', ':q_service'))
                    ->setParameter(':q_service', $p_fservice)
                    ->addGroupBy('service');
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Control $control
     * @return array
     */
    public function findFolderByControl(Control $control)
    {
        $qb = $this->queryFolder();

        $qb->where($qb->expr()->eq('folder.control', ':control'))
            ->setParameter(':control', $control);

        $qb->orderBy('folder.num', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find complete Folders associated to Minute
     * use on Minute show
     *
     * @param Minute $minute
     * @return array
     */
    public function findByMinute(Minute $minute)
    {
        $qb = $this->queryFolder();

        $qb->where($qb->expr()->eq('folder.minute', ':minute'))
            ->setParameter(':minute', $minute);

        $qb->orderBy('folder.num', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find small Folders associated to Minute
     * use on Minute show
     *
     * @param Minute $minute
     * @return array
     */
    public function findSmallFolderByMinute(Minute $minute)
    {
        $qb = $this->createQueryBuilder('folder')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute');

        $qb->where($qb->expr()->eq('folder.minute', ':minute'))
            ->setParameter(':minute', $minute);

        $qb->orderBy('folder.num', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find last folder by minute
     * use on Minute show
     *
     * @param Minute $p_minute
     * @param false $p_isCommand
     * @return int|mixed|string
     */
    public function findLastByMinute(Minute $p_minute, bool $p_isCommand = false)
    {
        $qb = $this->queryFolder();

        $qb->where($qb->expr()->eq('folder.minute', ':minute'))
            ->setParameter(':minute', $p_minute);

        $qb->orderBy('folder.updatedAt', 'ASC');
        $qb->setMaxResults(1);

        if ($p_isCommand == true) {
            $qb->select([
                'partial folder.{id}',
            ]);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find max num used for 1 minute
     * Use on Code generator
     *
     * @param $prefix
     * @return mixed
     */
    public function findMaxNumForMinute($prefix)
    {
        $qb = $this->createQueryBuilder('folder');

        $qb->where($qb->expr()->like('folder.num', ':num'))
            ->setParameter('num', '%' . $prefix . '%');

        $qb->select($qb->expr()->max('folder.num'));

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Folder Repository - ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Find all folders between 2 ids to avoid issues when work on huge database
     *
     * @param $p_startId
     * @param $p_endId
     * @return int|mixed|string
     */
    public function findAllBetweenId($p_startId, $p_endId)
    {
        $qb = $this->queryFolderCommand();

        $qb->andWhere($qb->expr()->between('folder.id', ':q_startId', ':q_endId'))
            ->setParameter('q_startId', $p_startId)
            ->setParameter('q_endId', $p_endId);

        return $qb->getQuery()->getResult();
    }
    /*******************************************************************************************/
    /********************* Custom find for test methods *****/
    /*******************************************************************************************/


    /**
     * Find one Folder for unit test with no dateClosure
     *
     * @return false|int|mixed|string|null
     */
    public function findOneForTest()
    {
        $qb = $this->queryFolder();

        $qb->andWhere($qb->expr()->isNull('folder.dateClosure'))
            ->andWhere($qb->expr()->eq('folder.type', ':q_type'))
            ->setParameter(':q_type', Folder::TYPE_FOLDER);

        $qb->andWhere($qb->expr()->isNull('adherent.logo'));

        $qb->setMaxResults(1);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Folder Repository - ' . $e->getMessage();
            return false;
        }
    }
    /*******************************************************************************************/
    /***************************************** Filters  ***************************************/
    /*******************************************************************************************/

    /**
     * Get folder by adherent, state and localized
     *
     * @param Adherent|null $adherent
     * @return mixed
     */
    public function filterByAdherentAndStateLocalized(Adherent $adherent = null)
    {
        $qb = $this->queryFolder();

        $qb->where($qb->expr()->isNotNull('plot.latitude'))
            ->andWhere($qb->expr()->isNotNull('plot.longitude'));

        $qb->andWhere($qb->expr()->eq('folder.type', ':q_type'))
            ->setParameter(':q_type', Folder::TYPE_FOLDER);

        if ($adherent) {
            // Call Trait
            $qb = $this->getValuesAdherent($adherent, $qb);
        }

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Folder dependencies
     *
     * @return array
     */
    public function findAll()
    {
        $qb = $this->queryFolder();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Folder dependencies
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return bool|mixed|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $qb = $this->queryFolderSimple();

        $qb->where($qb->expr()->eq('folder.id', ':q_folder'))
            ->setParameter(':q_folder', $id);

//      Try to comment this because it break the memory limit. Keep it commented for now in case it break something to remove it

//        $qb->select(array(
//            'partial folder.{id, num, nature, dateClosure, reasonObstacle, ascertainment}',
//            'partial control.{id, dateControl, stateControl}',
//            'partial elements.{id, position, state, name, image, comment}',
//            'partial tagsNature.{id, name}',
//            'partial tagsTown.{id, name}',
//            'partial natinfs.{id, num, qualification, definedBy, repressedBy}',
//            'partial minute.{id, num}',
//            'partial plot.{id, latitude, longitude, address, place, parcel}',
//            'partial plot_town.{id, name, code}',
//            'partial adherent.{id, name, logo}',
//            'partial town.{id, name, code}',
//            'partial intercommunal.{id, name, code}',
//            'partial service.{id, name}',
//            'partial humansByFolder.{id, name, firstname, status, person}',
//            'partial humansByMinute.{id, name, firstname, status, person}',
//        ));

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Folder Repository - ' . $e->getMessage();
            return false;
        }
    }
    /*******************************************************************************************/
    /********************* Query - Dependencies of Folder Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     * Used for command to clean html
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryFolderSimple()
    {
        $qb = $this->createQueryBuilder('folder')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute')
            ->leftJoin('folder.natinfs', 'natinfs')->addSelect('natinfs');

        return $qb;
    }

    /**
     * Classic dependencies
     * Used for command to clean html
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryFolderCommand()
    {
        $qb = $this->createQueryBuilder('folder')
            ->leftJoin('folder.edition', 'edition')->addSelect('edition')
            ->leftJoin('folder.control', 'control')->addSelect('control')
            ->leftJoin('control.editions', 'controlEditions')->addSelect('controlEditions')
            ->leftJoin('folder.courier', 'courier')->addSelect('courier')
            ->leftJoin('courier.edition', 'courierEditions')->addSelect('courierEditions')
            ->leftJoin('courier.humansEditions', 'courierHumansEditions')->addSelect('courierHumansEditions')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute')
            ->leftJoin('minute.updatings', 'updatings')->addSelect('updatings')
            ->leftJoin('minute.closure', 'closure')->addSelect('closure')
            ->leftJoin('minute.decisions', 'decisions')->addSelect('decisions');

        return $qb;
    }

    /**
     * Classic dependencies
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryFolder()
    {
        $qb = $this->createQueryBuilder('folder')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute')
            ->leftJoin('minute.plot', 'plot')->addSelect('plot')
            ->leftJoin('plot.town', 'plot_town')->addSelect('plot_town')
            ->leftJoin('plot_town.intercommunal', 'plot_intercommunal')->addSelect('plot_intercommunal')
            ->leftJoin('minute.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('adherent.user', 'user')
            ->leftJoin('adherent.town', 'town')
            ->leftJoin('adherent.intercommunal', 'intercommunal')
            ->leftJoin('adherent.service', 'service')
            ->leftJoin('folder.control', 'control')->addSelect('control')
            ->leftJoin('folder.natinfs', 'natinfs')->addSelect('natinfs')
            ->leftJoin('folder.tagsNature', 'tagsNature')->addSelect('tagsNature')
            ->leftJoin('folder.tagsTown', 'tagsTown')->addSelect('tagsTown')
            ->leftJoin('folder.humansByFolder', 'humansByFolder')->addSelect('humansByFolder')
            ->leftJoin('folder.humansByMinute', 'humansByMinute')->addSelect('humansByMinute')
            ->leftJoin('folder.elements', 'elements')->addSelect('elements')
            ->leftJoin('folder.edition', 'edition')->addSelect('edition');

        return $qb;
    }

    /**
     * Classic dependencies
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryFolderBrowser()
    {
        $qb = $this->createQueryBuilder('folder')
            ->leftJoin('folder.tagsNature', 'tagsNature')->addSelect('tagsNature')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute')
            ->leftJoin('minute.humans', 'humans')->addSelect('humans')
            ->leftJoin('minute.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('adherent.town', 'town')->addSelect('town')
            ->leftJoin('adherent.intercommunal', 'intercommunal')->addSelect('service')
            ->leftJoin('adherent.service', 'service')->addSelect('service')
            ->leftJoin('minute.plot', 'plot')->addSelect('plot')
            ->leftJoin('plot.town', 'plot_town')->addSelect('plot_town')
            ->leftJoin('plot_town.intercommunal', 'plot_intercommunal')->addSelect('plot_intercommunal');

        $qb->leftJoin('minute.decisions', 'decisions')->addSelect('decisions')
            ->leftJoin('decisions.tribunalCommission', 'tribunalCommission')->addSelect('tribunalCommission')
            ->leftJoin('decisions.appealCommission', 'appealCommission')->addSelect('appealCommission')
            ->leftJoin('decisions.expulsion', 'expulsion')->addSelect('expulsion')
            ->leftJoin('decisions.demolition', 'demolition')->addSelect('demolition');

        return $qb;
    }

}
