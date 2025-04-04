<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

use Lucca\Bundle\CoreBundle\Repository\ToggleableRepository;

class FolderRepository extends EntityRepository
{
    /** Traits */
    use ToggleableRepository;

    /**
     * Find Folder by path
     */
    public function findByPath($path): mixed
    {
        $qb = $this->createQueryBuilder('folder');

        $qb->where($qb->expr()->eq('folder.path', ':q_path'))
            ->setParameter(':q_path', $path);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e;
        }
    }
}
