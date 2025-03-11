<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class CourierHumanEditionRepository extends EntityRepository
{
    /**
     * Find one courierHumanEdition for unit test
     */
    public function findOneEditionForTest(): mixed
    {
        $qb = $this->createQueryBuilder('courierHumanEdition')
            ->leftJoin('courierHumanEdition.courier', 'courier')->addSelect('courier');

        $qb->andWhere($qb->expr()->isNull('courier.dateOffender'));
        $qb->andWhere($qb->expr()->isNotNull('courier.edition'));
        $qb->setMaxResults(1);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - CourierHumanEditionRepository Repository - ' . $e->getMessage();

            return false;
        }
    }
}
