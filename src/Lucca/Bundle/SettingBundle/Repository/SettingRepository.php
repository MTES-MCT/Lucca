<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Repository;

use Doctrine\ORM\{EntityRepository, NonUniqueResultException, QueryBuilder};

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\SettingBundle\Entity\Setting;

class SettingRepository extends EntityRepository
{

    /**
     * Override findAll method
     * with Setting dependencies
     */
    public function findAllByRole(bool $isSuperAdmin = true): array
    {
        $qb = $this->querySetting();

        if (!$isSuperAdmin) {
            $qb->andWhere($qb->expr()->eq('setting.accessType', ':q_accessType'))
                ->setParameter(':q_accessType', Setting::ACCESS_TYPE_ADMIN);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Override findAll
     */
    public function findAllOptimized(null|Department|int $department): array
    {
        $qb = $this->createQueryBuilder('setting');
        $qb->select('PARTIAL setting.{id, type, name, value}');
        $qb->orderBy('setting.name', 'ASC');

        $qb->where($qb->expr()->eq('setting.department', ':q_department'))
            ->setParameter(':q_department', $department);

        return $qb->getQuery()->getArrayResult();
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Setting dependencies
     */
    public function findAll(): array
    {
        $qb = $this->querySetting();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Setting dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->querySetting();

        $qb->where($qb->expr()->eq('setting.id', ':q_setting'))
            ->setParameter(':q_setting', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Setting Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Setting Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function querySetting(): QueryBuilder
    {
        return $this->createQueryBuilder('setting')
            ->leftJoin('setting.category', 'category')->addSelect('category')
        ;
    }
}
