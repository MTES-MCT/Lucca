<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Repository;

use Doctrine\ORM\{EntityRepository, NonUniqueResultException, QueryBuilder};
use Doctrine\ORM\Tools\Pagination\Paginator;

use Lucca\Bundle\CoreBundle\Repository\ToggleableRepository;
use Lucca\Bundle\MediaBundle\Entity\Gallery;

class MediaRepository extends EntityRepository
{
    /** Traits */
    use ToggleableRepository;

    /**
     * Function used to return the first media in order to generate file path in path formatter
     */
    public function findFirstMedia(): mixed
    {
        $qb = $this->queryMedia();
        $qb->setMaxResults(1);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e;
        }
    }

    public function findMediasByGallery(Gallery $gallery): mixed
    {
        $qb = $this->queryMedia();

        $qb->where($qb->expr()->eq('media.nameCanonical', ':nameCanonical'));

        $qb->setParameter(':gallery', $gallery);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e;
        }
    }

    /**
     * Find max num used with specific prefix
     */
    public function findMaxNumberWithPrefix($prefix): mixed
    {
        $qb = $this->queryMedia();

        $qb->where($qb->expr()->like('media.nameCanonical', ':q_prefix'))
            ->setParameter(':q_refix', "%$prefix%");

        $qb->select($qb->expr()->max('media.nameCanonical'));

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e;
        }
    }

    public function findOneFileByName($name): mixed
    {
        $qb = $this->queryMedia();

        $qb->where($qb->expr()->eq('media.nameCanonical', ':q_nameCanonical'))
            ->setParameter(':q_nameCanonical', $name);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e;
        }
    }

    /*******************************************************************************************/
    /************************************** Paginate Queries ***********************************/
    /*******************************************************************************************/

    /**
     * Find all medias and paginate it.
     */
    public function findAllPaginate($page, $nbMaxPerPage): ?Paginator
    {
        if (!is_numeric($page) or $page < 1 or !is_numeric($nbMaxPerPage)) {
            return null;
        }

        $qb = $this->queryMedia();
        $qb->addOrderBy('media.id', 'DESC');

        $query = $qb->getQuery();

        $firstResult = ($page - 1) * $nbMaxPerPage;
        $query->setFirstResult($firstResult)->setMaxResults(20);
        $paginator = new Paginator($query);

        if (($paginator->count() <= $firstResult) && $page != 1) {
            return null;
        }

        return $paginator;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Media dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryMedia();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Media dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryMedia();

        $qb->where($qb->expr()->eq('media.id', ':q_media'))
            ->setParameter(':q_media', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Media Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Media Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryMedia(): QueryBuilder
    {
        return $this->createQueryBuilder('media')
            ->leftJoin('media.category', 'category')->addSelect('category')
            ->leftJoin('media.folder', 'folder')->addSelect('folder')
            ->leftJoin('media.owner', 'owner')->addSelect('owner')
            ->leftJoin('media.metas', 'metas')->addSelect('metas')
        ;
    }
}
