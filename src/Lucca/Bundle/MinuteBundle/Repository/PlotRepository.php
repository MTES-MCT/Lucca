<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class PlotRepository
 *
 * @package Lucca\Bundle\MinuteBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 */
class PlotRepository extends EntityRepository
{
    /**
     * Method used to find all plot never login and not lock
     *
     * @return array
     */
    public function findAllWithoutGeocode()
    {
        $qb = $this->queryPlot();

        $qb->where($qb->expr()->isNull('plot.latitude'));

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all plot without location from
     *
     * @return array
     */
    public function findAllWithoutLocationFrom()
    {
        $qb = $this->queryPlot();

        $qb->where($qb->expr()->orX(
            $qb->expr()->isNull('plot.locationFrom'),
            $qb->expr()->eq('plot.locationFrom', ":q_empty"))
        )->setParameter(':q_empty', "");

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Department Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryPlot()
    {
        $qb = $this->createQueryBuilder('plot');

        return $qb;
    }
}
