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

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Updating;
use Doctrine\ORM\EntityManager;

/**
 * Class NumUpdatingGenerator
 *
 * @package Lucca\MinuteBundle\Generator
 * @author Terence <terence@numeric-wave.tech>
 */
class NumUpdatingGenerator
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
     * $suffix - take 'PV'
     * $increment - Take the last code + 1
     *
     * @param Updating $updating
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function generate(Updating $updating)
    {
        $prefix = $updating->getMinute()->getNum() . '-RC-';

        $maxCode = $this->em->getRepository('LuccaMinuteBundle:Updating')
            ->findMaxNumForMinute($prefix);

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
        return 'lucca.generator.updating_num';
    }
}