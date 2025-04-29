<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;

use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;

class AreaRepository extends LuccaRepository
{
    /**
     * Find area for dashboard display
     * Or render display
     *
     * @throws NonUniqueResultException
     */
    public function findDashboard($position = null): null|object|array
    {
        $qb = $this->createQueryBuilder('area')
            ->leftJoin('area.subareas', 'subarea')->addSelect('subarea')
            ->leftJoin('subarea.pages', 'page')->addSelect('page')
            ->leftJoin('page.author', 'author')->addSelect('author');

        $qb->orderBy('area.id, subarea.position, page.position', 'ASC');
        $qb->where($qb->expr()->eq('page.enabled', ':q_page'))
            ->andWhere($qb->expr()->eq('subarea.enabled', ':q_subarea'))
            ->andWhere($qb->expr()->eq('area.enabled', ':q_area'))
            ->setParameter(':q_area', true)
            ->setParameter(':q_subarea', true)
            ->setParameter(':q_page', true);

        if ($position != null) {
            $qb->andWhere($qb->expr()->eq('area.position', ':position'))
                ->setParameter(':position', $position);

            try {
                return $qb->getQuery()->getOneOrNullResult();
            } catch (NonUniqueResultException) {
                // Return null to avoid throwing an exception when there is no department filter
                return null;
            }
        }

        return $qb->getQuery()->getResult();
    }
}
