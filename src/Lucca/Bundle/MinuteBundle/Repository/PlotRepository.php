<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Repository;

use Doctrine\ORM\{EntityRepository, QueryBuilder};

class PlotRepository extends EntityRepository
{
    /**
     * Method used to find all plot never login and not lock
     */
    public function findAllWithoutGeocode(): array
    {
        $qb = $this->queryPlot();

        $qb->where($qb->expr()->isNull('plot.latitude'));

        return $qb->getQuery()->getResult();
    }

    /**
     * Method used to find all plot without location from
     */
    public function findAllWithoutLocationFrom(): array
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
     */
    private function queryPlot(): QueryBuilder
    {
        return $this->createQueryBuilder('plot');
    }
}
