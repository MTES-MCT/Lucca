<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Manager;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\MinuteBundle\Entity\Minute;

class StatisticsManager
{
    /**
     * Create array useful in stats
     */
    public function createStatArray($p_adherents, $p_minutes): array
    {
        /** Create array that will be useful */
        $result = [];
        $result['countAdherent'] = 0;
        $result['countTownAdherent'] = 0;
        $result['countMinutes'] = 0;
        $result['byTown'] = [];

        /** @var Adherent $adherent */
        foreach ($p_adherents as $adherent) {
            if ($adherent->getTown()) {
                $result['countAdherent'] += 1;
                $town = $adherent->getTown()->getName();
                if (!array_key_exists($town, $result['byTown'])) {
                    $result['countTownAdherent'] += 1;
                    $result['byTown'][$town]['countAdherent'] = 0;
                    $result['byTown'][$town]['countMinuteOpen'] = 0;
                    $result['byTown'][$town]['countMinuteClosed'] = 0;
                }
                $result['byTown'][$town]['countAdherent'] += 1;
            }
        }

        /** @var Minute $minute */
        foreach ($p_minutes as $minute) {
            $result['countMinutes'] += 1;
            if ($minute->getAdherent()->getTown()) {
                $city = $minute->getAdherent()->getTown()->getName();

                /** If the town is not already  */
                if (!array_key_exists($city, $result['byTown'])) {
                    $result['byTown'][$city] = [];
                    $result['byTown'][$city]['countAdherent'] = 0;
                    $result['byTown'][$city]['countMinuteOpen'] = 0;
                    $result['byTown'][$city]['countMinuteClosed'] = 0;
                }

                if ($minute->getClosure()) {
                    $result['byTown'][$city]['countMinuteClosed'] += 1;
                } else {
                    $result['byTown'][$city]['countMinuteOpen'] += 1;
                }
            }
        }

        /** Order towns */
        ksort($result['byTown']);

        /** Return an array like :
         *  "countAdherent" => 44
         *  "countTownAdherent" => 16
         *  "byTown" => [
         *     "BESSAN" =>  [
         *        "countAdherent" => 3
         *        "countMinuteOpen" => 40
         *        "countMinuteClosed" => 3
         *     ]
         *  ]
         */
        return $result;
    }

    public function getName(): string
    {
        return 'lucca.manager.adherent_statistics';
    }
}
