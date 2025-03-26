<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Lucca\Bundle\FolderBundle\Entity\Folder;

readonly class NumFolderGenerator
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    /**
     * Define automatic code for Folder
     * Format :
     * $minute->getNum() - take num Minute
     * $suffix - take 'RC'
     * $increment - Take the last code + 1
     */
    public function generate(Folder $folder): string
    {
        if ($folder->getType() === Folder::TYPE_FOLDER) {
            /** Classic Folder */
            $prefix = $folder->getMinute()->getNum() . '-PV-';
        } elseif ($folder->getType() === Folder::TYPE_REFRESH) {
            /** Refresh Folder */
            $prefix = $folder->getMinute()->getNum() . '-PVR-';
        } else {
            throw new NotFoundHttpException('Bad Folder type specify');
        }

        $maxCode = $this->em->getRepository(Folder::class)->findMaxNumForMinute($prefix);

        if ($maxCode) {
            $increment = substr($maxCode[1], -2);
            $increment = (int)$increment + 1;
        } else {
            $increment = 0;
        }

        return $prefix . sprintf('%02d', $increment);
    }
}
