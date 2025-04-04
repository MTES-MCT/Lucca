<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Utils;

use Doctrine\ORM\EntityManagerInterface;

use Lucca\Bundle\ParameterBundle\Entity\Town;

readonly class GeneralUtils
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    /**
     * get all Towns from adherents
     */
    public function getTownByAdherents($adherents): array
    {
        $towns = [];
        $intercommunals = [];

        foreach ($adherents as $adherent) {
            if ($adherent && $adherent->getTown() != null && !in_array($adherent->getTown(), $towns)) {
                $towns[] = $adherent->getTown();
            } elseif ($adherent && $adherent->getIntercommunal() != null && !in_array($adherent->getIntercommunal(), $intercommunals)) {
                $intercommunals[] = $adherent->getIntercommunal();
            }
            $towns = [...$towns, ...$this->em->getRepository(Town::class)->findAllByIntercommunals($intercommunals, $towns)];

        }

        return $towns;
    }
}
