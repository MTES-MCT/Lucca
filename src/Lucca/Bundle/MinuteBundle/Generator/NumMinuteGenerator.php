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

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Doctrine\ORM\EntityManager;

/**
 * Class NumMinuteGenerator
 *
 * @package Lucca\MinuteBundle\Generator
 * @author Terence <terence@numeric-wave.tech>
 */
class NumMinuteGenerator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * NumMinuteGenerator constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Define automatic code for Minute
     * Format :
     * $year - take 2 letters (17)
     * $siren - take 'siren' of authority
     * $increment - Take the last code + 1
     *
     * @param Minute $minute
     * @return string
     */
    public function generate(Minute $minute)
    {
        /** If a date Complaint has been defined - take this year and use it to generate the minute num */
        if (!$minute->getDateComplaint()) {
            $now = new \DateTime();
            $year = $now->format('y');
        } else
            $year = $minute->getDateComplaint()->format('y');

        $adherent = $minute->getAdherent();
        $authority = null;

        if ($adherent->getTown())
            $authority = $adherent->getTown()->getCode();
        elseif ($adherent->getIntercommunal())
            $authority = $adherent->getIntercommunal()->getCode();
        elseif ($adherent->getService())
            $authority = $adherent->getService()->getCode();

        $prefix = $year . '-' . $authority . '-';

        $maxCode = $this->em->getRepository('LuccaMinuteBundle:Minute')->findMaxNumForYear($prefix);

        if ($maxCode) {
            $increment = substr($maxCode[1], -3);
            $increment = (int)$increment + 1;

        } else
            $increment = 0;

        $code = $prefix . sprintf('%03d', $increment);

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.generator.minute_num';
    }
}