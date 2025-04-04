<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */


namespace Lucca\Bundle\DepartmentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Lucca\Bundle\CoreBundle\Repository\ToggleableRepository;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

class DepartmentRepository extends EntityRepository
{

    /** Traits */
    use ToggleableRepository;

    /*******************************************************************************************/
    /********************* Get methods *****/
    /*******************************************************************************************/

    public function getByIds(?array $ids): QueryBuilder
    {
        $qb = $this->queryDepartment();

        $this->getActiveDepartments($qb);

        if ($ids !== null)
            $qb->andWhere($qb->expr()->in('department.id', ':q_ids'))
                ->setParameter(':q_ids', $ids);

        return $qb;

    }

    /*******************************************************************************************/
    /********************* Filter methods *****/
    /*******************************************************************************************/

    public function getActiveDepartments(QueryBuilder $qb, bool $enable = true): QueryBuilder
    {
        $qb->andWhere($qb->expr()->eq('department.enable', ':q_enable'))
            ->setParameter(':q_enable', $enable);

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Department dependencies
     *
     * @return array
     */
    public function findAll(): array
    {
        $qb = $this->queryDepartment();

        return $qb->getQuery()->getResult();
    }

    /**
     * findHelpTextByDepartment method
     *
     * @param Department $department
     * @return array
     */
    public function findHelpTextByDepartment(Department $department): array
    {
        $qb = $this->queryDepartment();

        $qb->select('department.helpLoginText')
            ->where($qb->expr()->eq('department.id', ':q_department'))
            ->setParameter(':q_department', $department->getId());

        return $qb->getQuery()->getResult();
    }


    /**
     * findHelpTextByDepartment method
     *
     * @param Department $department
     * @return array
     */
    public function findAllForHelpText(): array
    {
        $qb = $this->queryDepartment();

        $qb->select('department.id, department.name', 'department.code');

        $qb->andWhere($qb->expr()->isNotNull('department.helpLoginText'));

        return $qb->getQuery()->getResult();
    }


    /**
     * Override find method
     * with Department dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryDepartment();

        $qb->where($qb->expr()->eq('department.id', ':q_department'))
            ->setParameter(':q_department', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Department Repository - ' . $e->getMessage();

            return null;
        }
    }

    /**
     * Classic dependencies
     *
     * @return QueryBuilder
     */
    private function queryDepartment(): QueryBuilder
    {
        return $this->createQueryBuilder('department');
    }

}
