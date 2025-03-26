<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Generator;

use Doctrine\ORM\{EntityManagerInterface, NonUniqueResultException};

use Lucca\Bundle\MinuteBundle\Entity\Updating;

readonly class NumUpdatingGenerator
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * Define automatic code for Folder
     * Format :
     * $minute->getNum() - take num Minute
     * $suffix - take 'PV'
     * $increment - Take the last code + 1
     *
     * @throws NonUniqueResultException
     */
    public function generate(Updating $updating): string
    {
        $prefix = $updating->getMinute()->getNum() . '-RC-';

        $maxCode = $this->em->getRepository(Updating::class)
            ->findMaxNumForMinute($prefix);

        if ($maxCode) {
            $increment = substr($maxCode[1], -2);
            $increment = (int)$increment + 1;
        } else {
            $increment = 0;
        }

        return $prefix . sprintf('%02d', $increment);
    }
}
