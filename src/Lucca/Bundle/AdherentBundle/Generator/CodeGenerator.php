<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Generator;

use Exception;
use Doctrine\ORM\EntityManagerInterface;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

readonly class CodeGenerator
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    /**
     * Define automatic code for Adherent
     * Format :
     * $label - 'lucca-'
     * $baseCode - code of town / intercommunal / partner
     * $increment - 2 num
     *
     * @throws Exception
     */
    public function generate(Adherent $entity): string
    {
        $label = strtolower(SettingManager::get('setting.general.prefixUsername.name')) . "-";

        if ($entity->getTown()) {
            $baseCode = $entity->getTown()->getCode();
        } elseif ($entity->getIntercommunal()) {
            $baseCode = $entity->getIntercommunal()->getCode();
        } elseif ($entity->getService()?->getCode()) {
            $baseCode = $entity->getService()->getCode();
        } else {
            throw new Exception('No code can be generated without location');
        }

        $prefix = $label . strtolower($baseCode) . '-';
        $maxCode = $this->em->getRepository(Adherent::class)->findMaxUsername($prefix);

        if ($maxCode) {
            $increment = substr($maxCode[1], -2);
            $increment = (int)$increment + 1;
        } else {
            $increment = 0;
        }

        return $prefix . sprintf('%02d', $increment);
    }
}
