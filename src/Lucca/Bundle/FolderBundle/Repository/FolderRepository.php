<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Repository;

use Doctrine\ORM\{EntityRepository, NonUniqueResultException, QueryBuilder};

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\CoreBundle\Repository\AdherentRepository;
use Lucca\Bundle\CoreBundle\Repository\ArrayLikeFilterTrait;
use Lucca\Bundle\CoreBundle\Repository\PaginationTrait;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Minute;

class FolderRepository extends EntityRepository
{
    /** Traits */
    use AdherentRepository;
    use ArrayLikeFilterTrait;
    use PaginationTrait;

    /*******************************************************************************************/
    /********************* Stats methods *****/
    /*******************************************************************************************/

    /**
     * Count type of decision between date of minute opening
     */
    public function findBetweenDates($minutes = null): mixed
    {
        $qb = $this->queryFolderSimple();

        /************** Filters on minute to get ******************/
        if ($minutes && count($minutes) > 0) {
            $qb->andWhere($qb->expr()->in('minute', ':q_minutes'))
                ->setParameter(':q_minutes', $minutes);
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
     */
    public function statFolders($natinf = null, $nature = null, $dateStartClosure = null, $dateEndClosure = null, $controls = null): mixed
    {
        $qb = $this->queryFolder();

        if ($natinf !== null && count($natinf) > 0) {
            $qb->andWhere($qb->expr()->in('natinfs', ':q_natinf'))
                ->setParameter(':q_natinf', $natinf);
        }

        if ($nature !== null && count($nature) > 0) {
            $qb->andWhere($qb->expr()->in('folder.nature', ':q_nature'))
                ->setParameter(':q_nature', $nature);
        }

        if ($dateStartClosure !== null && $dateEndClosure !== null) {
            $qb->andWhere($qb->expr()->between('folder.dateClosure', ':q_startClosure', ':q_endClosure'))
                ->setParameter(':q_startClosure', $dateStartClosure)
                ->setParameter(':q_endClosure', $dateEndClosure);
        }

        if ($controls != null && count($controls) > 0) {
            $qb->andWhere($qb->expr()->in('folder.control', ':q_control'))
                ->setParameter(':q_control', $controls);
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
     */
    public function findAllWithGeocodeDashboard(?Adherent $p_adherent = null): array
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
     */
    public function findByTown($town, ?Adherent $adherent = null): mixed
    {
//        $qb = $this->filterByAdherentAndStateLocalized($p_adherent);
        $qb = $this->queryFolder();

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->in('town', ':q_town'),
                $qb->expr()->in('plot_town', ':q_town')
            ))
            ->setParameter(':q_town', $town);

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
     */
    public function findByIds(array $ids, string $orderBy = null, ?int $limit = null, ?int $offset = null): mixed
    {
        $qb = $this->queryFolder();

        $qb->where($qb->expr()->in('folder.id', ':q_folder'))
            ->setParameter(':q_folder', $ids);

        if ($orderBy) {
            $qb->orderBy($orderBy);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all open folders with geo code
     */
    public function findAllWithGeocode(?Adherent $p_adherent = null): array
    {
        $qb = $this->filterByAdherentAndStateLocalized($p_adherent);

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all open folders with geo code in a specific area
     */
    public function findAllInArea($minLat, $maxLat, $nLon, $maxLon, Adherent $adherent = null, $maxResults = null, $minutes = null): mixed
    {
        $qb = $this->filterByAdherentAndStateLocalized($adherent);

        $qb->andWhere($qb->expr()->between('plot.latitude', ':q_minLat', ':q_maxLat'))
            ->andWhere($qb->expr()->between('plot.longitude', ':q_minLon', ':q_maxLon'))
            ->setParameter('q_minLat', $minLat)
            ->setParameter('q_maxLat', $maxLat)
            ->setParameter('q_minLon', $nLon)
            ->setParameter('q_maxLon', $maxLon);

        if ($minutes && count($minutes) > 0) {
            $qb->andwhere($qb->expr()->in('folder.minute', ':q_minutes'))
                ->setParameter(':q_minutes', $minutes);
        }

        if ($maxResults) {
            $qb->groupBy('folder');
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find Minutes with browser's filters.
     * Used on Minute browser view
     */
    public function findFolderBrowser(?Adherent $adherent = null, $fdateStart = null, $fdateEnd = null, $fnum = null,
                        $fnumFolder = null, $fadherent = null, $ftown = null, $finterco = null, $fservice = null): mixed
    {
        $qb = $this->queryFolderBrowser();

        $qb->andWhere($qb->expr()->between('minute.dateOpening', ':q_start', ':q_end'))
            ->setParameter(':q_start', $fdateStart)
            ->setParameter(':q_end', $fdateEnd);

        $qb->orderBy('minute.dateOpening', 'ASC')
            ->addGroupBy('folder');

        if ($fnum) {
            $qb->andWhere($qb->expr()->like('minute.num', ':q_num'))
                ->setParameter(':q_num', '%' . $fnum . '%');
        }

        if ($fnumFolder) {
            $qb->andWhere($qb->expr()->like('folder.num', ':q_num'))
                ->setParameter(':q_num', '%' . $fnumFolder . '%');
        }

        if ($adherent) {
            // Call Trait
            $qb = $this->getValuesAdherent($adherent, $qb);
        } else {
            if ($fadherent && count($fadherent) > 0) {
                $qb->andWhere($qb->expr()->in('adherent', ':q_adherent'))
                    ->setParameter(':q_adherent', $fadherent)
                    ->addGroupBy('adherent');
            }

            if ($ftown && count($ftown) > 0) {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->in('town', ':q_town'),
                        $qb->expr()->in('plot_town', ':q_town')
                    ))
                    ->setParameter(':q_town', $ftown)
                    ->addGroupBy('town.name, plot_town.name');
            }

            if ($finterco && count($finterco) > 0) {
                $qb->andWhere($qb->expr()->in('intercommunal', ':q_intercommunal'))
                    ->setParameter(':q_intercommunal', $finterco)
                    ->addGroupBy('intercommunal');
            }

            if ($fservice && count($fservice) > 0) {
                $qb->andWhere($qb->expr()->in('service', ':q_service'))
                    ->setParameter(':q_service', $fservice)
                    ->addGroupBy('service');
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function findFolderByControl(Control $control): array
    {
        $qb = $this->queryFolder();

        $qb->where($qb->expr()->eq('folder.control', ':q_control'))
            ->setParameter(':q_control', $control);

        $qb->orderBy('folder.num', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find complete Folders associated to Minute
     * use on Minute show
     */
    public function findByMinute(Minute $minute): array
    {
        $qb = $this->queryFolder();

        $qb->where($qb->expr()->eq('folder.minute', ':q_minute'))
            ->setParameter(':q_minute', $minute);

        $qb->orderBy('folder.num', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find small Folders associated to Minute
     * use on Minute show
     */
    public function findSmallFolderByMinute(Minute $minute): array
    {
        $qb = $this->createQueryBuilder('folder')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute');

        $qb->where($qb->expr()->eq('folder.minute', ':q_minute'))
            ->setParameter(':q_minute', $minute);

        $qb->orderBy('folder.num', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find last folder by minute
     * use on Minute show
     */
    public function findLastByMinute(Minute $minute, bool $isCommand = false): mixed
    {
        $qb = $this->queryFolder();

        $qb->where($qb->expr()->eq('folder.minute', ':minute'))
            ->setParameter(':minute', $minute);

        $qb->orderBy('folder.updatedAt', 'ASC');
        $qb->setMaxResults(1);

        if ($isCommand) {
            $qb->select([
                'partial folder.{id}',
            ]);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find max num used for 1 minute
     * Use on Code generator
     */
    public function findMaxNumForMinute($prefix): mixed
    {
        $qb = $this->createQueryBuilder('folder');

        $qb->where($qb->expr()->like('folder.num', ':q_num'))
            ->setParameter('q_num', '%' . $prefix . '%');

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
     */
    public function findAllBetweenId($startId, $endId): mixed
    {
        $qb = $this->queryFolderCommand();

        $qb->andWhere($qb->expr()->between('folder.id', ':q_startId', ':q_endId'))
            ->setParameter('q_startId', $startId)
            ->setParameter('q_endId', $endId);

        return $qb->getQuery()->getResult();
    }


    /**
     * Find Folders for Rest API with filters and pagination
     */
    public function findForRestApi(array $filters = []): array
    {
        $qb = $this->queryFolder();
        //add left join to department
        $qb->leftJoin('folder.department', 'department')->addSelect('department');

        if (isset($filters['INSEE']) && count($filters['INSEE']) > 0) {
            $qb->andWhere($qb->expr()->in('plot_town.code', ':inseeCode'))
                ->setParameter('inseeCode', $filters['INSEE']);
        }

        if (isset($filters['townName'])) {
            $qb = $this->addArrayLikeFilter($qb, 'town.name', $filters['townName']);
        }

        if (isset($filters['plotCode']) && count($filters['plotCode']) > 0) {
            $orX = $qb->expr()->orX();

            foreach ($filters['plotCode'] as $i => $plotCode) {
                $paramExact  = 'plotCodeExact' . $i;
                $paramStart  = 'plotCodeStart' . $i;
                $paramMiddle = 'plotCodeMiddle' . $i;
                $paramEnd    = 'plotCodeEnd' . $i;

                // Check for exact match and partial matches in a comma-separated list
                $orX->add($qb->expr()->orX(
                    "plot.parcel = :$paramExact",         // exact
                    "plot.parcel LIKE :$paramStart",     // at the beginning
                    "plot.parcel LIKE :$paramMiddle",    // in the middle
                    "plot.parcel LIKE :$paramEnd"        // at the end
                ));

                $qb->setParameter($paramExact, $plotCode);
                $qb->setParameter($paramStart, $plotCode . ',%');        // at the beginning
                $qb->setParameter($paramMiddle, '%,' . $plotCode . ',%'); // in the middle
                $qb->setParameter($paramEnd, '%,' . $plotCode);          // at the end
            }

            $qb->andWhere($orX);
        }

        if (isset($filters['number'])) {
            $qb->andWhere('folder.num = :number')
                ->setParameter('number', $filters['number']);
        }

        //filter only enabled departments
        $qb->andWhere($qb->expr()->eq('department.enabled', true));

        // Pagination
        $page = isset($filters['page']) ? (int)$filters['page'] : 1;
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 30;

        // order by createdAt desc
        $qb->orderBy('folder.createdAt', 'DESC');

        return $this->paginate($qb, $page, $limit);
    }

    /*******************************************************************************************/
    /********************* Custom find for test methods *****/
    /*******************************************************************************************/

    /**
     * Find one Folder for unit test with no dateClosure
     */
    public function findOneForTest(): mixed
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
     */
    public function filterByAdherentAndStateLocalized(?Adherent $adherent = null): QueryBuilder
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
     */
    public function findAll(): array
    {
        $qb = $this->queryFolder();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Folder dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
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

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Folder Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     * Used for command to clean html
     */
    private function queryFolderSimple(): QueryBuilder
    {
        return $this->createQueryBuilder('folder')
            ->leftJoin('folder.minute', 'minute')->addSelect('minute')
            ->leftJoin('folder.natinfs', 'natinfs')->addSelect('natinfs');
    }

    /**
     * Classic dependencies
     * Used for command to clean html
     */
    private function queryFolderCommand(): QueryBuilder
    {
        return $this->createQueryBuilder('folder')
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
    }

    /**
     * Classic dependencies
     */
    private function queryFolder(): QueryBuilder
    {
        return $this->createQueryBuilder('folder')
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
    }

    /**
     * Classic dependencies
     */
    private function queryFolderBrowser(): QueryBuilder
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
