<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Repository;

use Doctrine\ORM\{EntityRepository, NonUniqueResultException, QueryBuilder};

use Lucca\Bundle\MediaBundle\Entity\Media;

class GalleryRepository extends EntityRepository
{
    /**
     * Find Gallery who used the Media
     * Or return null if the Media is never used by a Gallery
     */
    public function findWithThisMedia(Media $media): mixed
    {
        $qb = $this->queryGallery();

        $qb->where($qb->expr()->eq('medias', ':q_media'))
            ->orWhere($qb->expr()->eq('gallery.defaultMedia', ':q_media'))
            ->setParameter(':q_media', $media);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Gallery Repository - ' . $e->getMessage();
            return false;
        }
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Gallery dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryGallery();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Gallery dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryGallery();

        $qb->where($qb->expr()->eq('gallery.id', ':q_gallery'))
            ->setParameter(':q_gallery', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Gallery Repository - ' . $e->getMessage();
            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Gallery Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryGallery(): QueryBuilder
    {
        return $this->createQueryBuilder('gallery')
            ->leftJoin('gallery.defaultMedia', 'defaultMedia')->addSelect('defaultMedia')
            ->leftJoin('gallery.medias', 'medias')->addSelect('medias')
        ;
    }
}
