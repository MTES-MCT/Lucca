<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Generator;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class NumFolderGenerator
 *
 * @package Lucca\MinuteBundle\Generator
 * @author Terence <terence@numeric-wave.tech>
 */
class NumFolderGenerator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * NumFolderGenerator constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Define automatic code for Folder
     * Format :
     * $minute->getNum() - take num Minute
     * $suffix - take 'RC'
     * $increment - Take the last code + 1
     *
     * @param Folder $folder
     * @return string
     */
    public function generate(Folder $folder)
    {
        if ($folder->getType() === Folder::TYPE_FOLDER) {
            /** Classic Folder */
            $prefix = $folder->getMinute()->getNum() . '-PV-';

        } elseif ($folder->getType() === Folder::TYPE_REFRESH) {
            /** Refresh Folder */
            $prefix = $folder->getMinute()->getNum() . '-PVR-';

        } else
            throw new NotFoundHttpException('Bad Folder type specify');

        $maxCode = $this->em->getRepository('LuccaMinuteBundle:Folder')->findMaxNumForMinute($prefix);

        if ($maxCode) {
            $increment = substr($maxCode[1], -2);
            $increment = (int)$increment + 1;

        } else
            $increment = 0;

        $code = $prefix . sprintf('%02d', $increment);
        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.generator.updating_folder_num';
    }
}