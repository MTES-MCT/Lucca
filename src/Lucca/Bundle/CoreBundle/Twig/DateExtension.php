<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Lucca\Bundle\CoreBundle\Utils\ConverterIntStr;

class DateExtension extends AbstractExtension
{
    public function __construct(
        private readonly ConverterIntStr $converterIntStr,
    )
    {
    }

    /**
     * Get Twig filters
     * Filter - strftime
     */
    public function getFilters(): array
    {
        return array(
            new TwigFilter('strftime', [$this, 'strftime'], ['needs_environment' => true]),
            new TwigFilter('dateToLetterSimple', [$this, 'dateToLetterSimple'], ['needs_environment' => true]),
            new TwigFilter('dateToLetter', [$this, 'dateToLetter'], ['needs_environment' => true]),
            new TwigFilter('hourToLetter', [$this, 'hourToLetter'], ['needs_environment' => true]),
            new TwigFilter('dateToLetterMonth', [$this, 'dateToLetterMonth'], ['needs_environment' => true])
        );
    }

    /**
     * Display date on format
     * 'samedi 27/05/2017 10:15'
     */
    public function strftime($env, DateTime $date, string $format = "%A %d %B %Y", $timezone = null): string
    {
        setlocale(LC_TIME, 'fr_FR', 'fra');
        $date = twig_date_converter($env, $date, $timezone);

        return utf8_encode(strftime($format, $date->getTimestamp()));
    }

    /**
     * Display date on format 'samedi dix mai deux mille dix sept'
     *
     * TODO test locale on serveur for translate month
     */
    public function dateToLetterSimple($env, DateTime $date, $timezone = null): string
    {
        setlocale(LC_TIME, 'fr_FR', 'fra');

        $date = twig_date_converter($env, $date, $timezone);

        $yearNum = utf8_encode(strftime('%Y', $date->getTimestamp()));
        $dayNum = utf8_encode(strftime('%d', $date->getTimestamp()));

        /** get the day name with locale FR */
        $formatterDay = new \IntlDateFormatter(
            'fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL,
            'Europe/Paris', \IntlDateFormatter::GREGORIAN, 'EEEE'
        );
        $dayName = ucfirst($formatterDay->format($date));

        /** get the month name with locale FR */
        $formatterMonth = new \IntlDateFormatter(
            'fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL,
            'Europe/Paris', \IntlDateFormatter::GREGORIAN, 'MMMM'
        );
        $monthName = ucfirst($formatterMonth->format($date));

        return strtolower("$dayName $dayNum $monthName $yearNum");
    }

    /**
     * Display date on format 'Janvier'
     */
    public function dateToLetterMonth($env, DateTime $date, $timezone = null): string
    {
        setlocale(LC_TIME, 'fr_FR', 'fra');

        $date = twig_date_converter($env, $date, $timezone);

        /** get the month name with locale FR */
        $formatterMonth = new \IntlDateFormatter(
            'fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL,
            'Europe/Paris', \IntlDateFormatter::GREGORIAN, 'MMMM'
        );

        return ucfirst($formatterMonth->format($date));
    }

    /**
     * Display date on format 'samedi dix mai deux mille dix sept'
     *
     * TODO test locale on serveur for translate month
     */
    public function dateToLetter($env, DateTime $date, $timezone = null): string
    {
        setlocale(LC_TIME, 'fr_FR', 'fra');

        $date = twig_date_converter($env, $date, $timezone);

        $yearNum = utf8_encode(strftime('%Y', $date->getTimestamp()));
        $dayNum = utf8_encode(strftime('%d', $date->getTimestamp()));

        /** get the month name with locale FR */
        $formatterMonth = new \IntlDateFormatter(
            'fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL,
            'Europe/Paris', \IntlDateFormatter::GREGORIAN, 'MMMM'
        );
        $monthName = ucfirst($formatterMonth->format($date));

        $dateLetter = 'L\'an ' . $this->converterIntStr->convertIntToString($yearNum);
        $dateLetter .= ' et le ' . $this->converterIntStr->convertIntToString($dayNum);
        $dateLetter .= ' ' . $monthName;

        return $dateLetter;
    }

    /**
     * Display hour on format 'dix heures quinze'
     */
    public function hourToLetter($env, DateTime $date, $timezone = null): string
    {
        setlocale(LC_TIME, 'fr_FR', 'fra');
        $date = twig_date_converter($env, $date, $timezone);

        $hourNum = utf8_encode(strftime('%H', $date->getTimestamp()));
        $minNum = utf8_encode(strftime('%M', $date->getTimestamp()));

        $dateLetter = ' à ' . $this->converterIntStr->convertIntToString($hourNum) . ' heure(s)';
        $dateLetter .= ' ' . $this->converterIntStr->convertIntToString($minNum) . ' minute(s)';

        return $dateLetter;
    }
}
