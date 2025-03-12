<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Generator;

use Doctrine\ORM\EntityManagerInterface;

use Lucca\Bundle\MinuteBundle\Entity\Minute;

readonly class NumMinuteGenerator
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * Define automatic code for Minute
     * Format :
     * $year - take 2 letters (17)
     * $siren - take 'siren' of authority
     * $increment - Take the last code + 1
     */
    public function generate(Minute $minute): string
    {
        /** If a date Complaint has been defined - take this year and use it to generate the minute num */
        if (!$minute->getDateComplaint()) {
            $now = new \DateTime();
            $year = $now->format('y');
        } else {
            $year = $minute->getDateComplaint()->format('y');
        }

        $adherent = $minute->getAdherent();
        $authority = null;

        if ($adherent->getTown()) {
            $authority = $adherent->getTown()->getCode();
        } elseif ($adherent->getIntercommunal()) {
            $authority = $adherent->getIntercommunal()->getCode();
        } elseif ($adherent->getService()) {
            $authority = $adherent->getService()->getCode();
        }

        $prefix = $year . '-' . $authority . '-';

        $maxCode = $this->em->getRepository(Minute::class)->findMaxNumForYear($prefix);

        if ($maxCode) {
            $increment = substr($maxCode[1], -3);
            $increment = (int)$increment + 1;
        } else {
            $increment = 0;
        }

        return $prefix . sprintf('%03d', $increment);
    }

    public function getName(): string
    {
        return 'lucca.generator.minute_num';
    }
}
