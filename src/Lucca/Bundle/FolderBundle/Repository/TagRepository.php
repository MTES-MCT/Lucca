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

/**
 * Class TagRepository
 *
 * @package Lucca\Bundle\FolderBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 */
class TagRepository extends EntityRepository
{
    /**
     * Find values by category / All if no category given
     *
     * @param null $category
     * @return array
     */
    public function findValuesByCategory($category = null)
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
     * Get values by category / All if no category given
     *
     * @param null $category
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getValuesByCategory($category = null)
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
     *
     * @return array
     */
    public function findAll()
    {
        $qb = $this->queryTag();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Tag dependencies
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return bool|mixed|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $qb = $this->queryTag();

        $qb->where($qb->expr()->eq('tag.id', ':q_tag'))
            ->setParameter(':q_tag', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Tag Repository - ' . $e->getMessage();
            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Tag Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryTag()
    {
        $qb = $this->createQueryBuilder('tag')
            ->leftJoin('tag.proposals', 'proposals')->addSelect('proposals');

        return $qb;
    }
}
