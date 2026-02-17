<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Repository;

use Doctrine\ORM\{EntityRepository, NonUniqueResultException, QueryBuilder};
use Lucca\Bundle\DepartmentBundle\Entity\Department;

class TagRepository extends EntityRepository
{
    /**
     * Find values by category / All if no category given
     */
    public function findValuesByCategory($category = null): array
    {
        $qb = $this->queryTag();

        $qb->where($qb->expr()->eq('tag.enabled', ':q_enabled'))
            ->setParameter(':q_enabled', true);

        if ($category)
            $qb->andWhere($qb->expr()->eq('tag.category', ':q_category'))
                ->setParameter(':q_category', $category);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all tags by department and nums use for initialization
     */
    public function findAllByDepartmentAndNums(Department $department, array $nums): array
    {
        $qb = $this->queryTag();
        $qb->andWhere($qb->expr()->eq('tag.department', ':q_department'))
            ->setParameter(':q_department', $department);
        $qb->andWhere($qb->expr()->isNotNull('tag.num'));
        $qb->andWhere($qb->expr()->in('tag.num', ':q_num'))
            ->setParameter(':q_num', $nums);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get values by category / All if no category given
     */
    public function getValuesByCategory($category = null): QueryBuilder
    {
        $qb = $this->queryTag();

        $qb->where($qb->expr()->eq('tag.enabled', ':q_enabled'))
            ->setParameter(':q_enabled', true);

        if ($category)
            $qb->andWhere($qb->expr()->eq('tag.category', ':q_category'))
                ->setParameter(':q_category', $category);

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Tag dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryTag();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Tag dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryTag();

        $qb->where($qb->expr()->eq('tag.id', ':q_tag'))
            ->setParameter(':q_tag', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Tag Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Tag Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryTag(): QueryBuilder
    {
        return $this->createQueryBuilder('tag')
            ->leftJoin('tag.proposals', 'proposals')->addSelect('proposals');
    }
}
