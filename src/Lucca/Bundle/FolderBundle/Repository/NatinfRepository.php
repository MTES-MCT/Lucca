<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Repository;

use Doctrine\ORM\{QueryBuilder, EntityRepository, NonUniqueResultException};

use Lucca\Bundle\CoreBundle\Repository\ToggleableRepository;
use Lucca\Bundle\FolderBundle\Entity\Folder;

class NatinfRepository extends EntityRepository
{
    /** Traits */
    use  ToggleableRepository;

    public function findNatinfsByFolder(Folder $folder): array
    {
        $qb = $this->queryNatinf();

        $qb->where($qb->expr()->eq('natinf.enabled', ':q_enabled'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->in('tags', ':q_tagsNature'),
                $qb->expr()->in('tags', ':q_tagsTown')
            ))
            ->setParameter(':q_enabled', true)
            ->setParameter(':q_tagsNature', $folder->getTagsNature())
            ->setParameter(':q_tagsTown', $folder->getTagsTown());

        return $qb->getQuery()->getResult();
    }

    /**
     * Override findAll
     * Add select of tags
     */
    public function findAllByStatus($status = null): array
    {
        $qb = $this->queryNatinf();

        if ($status) {
            $qb->where($qb->expr()->eq('natinf.enabled', ':q_enabled'))
                ->setParameter(':q_enabled', $status);
        }

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Natinf dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryNatinf();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Natinf dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryNatinf();

        $qb->where($qb->expr()->eq('natinf.id', ':q_natinf'))
            ->setParameter(':q_natinf', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Natinf Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Natinf Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryNatinf(): QueryBuilder
    {
        return $this->createQueryBuilder('natinf')
            ->leftJoin('natinf.parent', 'parent')->addSelect('parent')
            ->leftJoin('natinf.tags', 'tags')->addSelect('tags');
    }
}
