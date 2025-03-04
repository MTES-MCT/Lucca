<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Repository;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Lucca\Bundle\CoreBundle\Repository\ToggleableRepository;

/**
 * Class NatinfRepository
 *
 * @package Lucca\Bundle\FolderBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 */
class NatinfRepository extends EntityRepository
{
    /** Traits */
    use  ToggleableRepository;

    /**
     * @param Folder $folder
     * @return array
     */
    public function findNatinfsByFolder(Folder $folder)
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
     *
     * @param null $status
     * @return array
     */
    public function findAllByStatus($status = null)
    {
        $qb = $this->queryNatinf();

        if ($status)
            $qb->where($qb->expr()->eq('natinf.enabled', ':q_enabled'))
                ->setParameter(':q_enabled', $status);

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Natinf dependencies
     *
     * @return array
     */
    public function findAll()
    {
        $qb = $this->queryNatinf();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Natinf dependencies
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return bool|mixed|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $qb = $this->queryNatinf();

        $qb->where($qb->expr()->eq('natinf.id', ':q_natinf'))
            ->setParameter(':q_natinf', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Natinf Repository - ' . $e->getMessage();
            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Natinf Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryNatinf()
    {
        $qb = $this->createQueryBuilder('natinf')
            ->leftJoin('natinf.parent', 'parent')->addSelect('parent')
            ->leftJoin('natinf.tags', 'tags')->addSelect('tags');

        return $qb;
    }
}
