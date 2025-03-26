<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Utils;

class ConverterIntStr
{
    /**
     * Convert an int to string
     * French language
     */
    public function convertIntToString($number): string
    {
        $joakim = explode('.', $number);

        if (isset($joakim[1]) && $joakim[1] != '') {
            return $this->convertIntToString($joakim[0]) . ' virgule ' . $this->convertIntToString($joakim[1]);
        }

        if ($number < 0) {
            return 'moins ' . $this->convertIntToString(-$number);
        }

        if ($number < 17) {
            switch ($number) {
                case 0:
                    return 'zero';
                case 1:
                    return 'un';
                case 2:
                    return 'deux';
                case 3:
                    return 'trois';
                case 4:
                    return 'quatre';
                case 5:
                    return 'cinq';
                case 6:
                    return 'six';
                case 7:
                    return 'sept';
                case 8:
                    return 'huit';
                case 9:
                    return 'neuf';
                case 10:
                    return 'dix';
                case 11:
                    return 'onze';
                case 12:
                    return 'douze';
                case 13:
                    return 'treize';
                case 14:
                    return 'quatorze';
                case 15:
                    return 'quinze';
                case 16:
                    return 'seize';
            }
        } else if ($number < 20) {
            return 'dix-' . $this->convertIntToString($number - 10);
        } else if ($number < 100) {
            if ($number % 10 == 0) {
                switch ($number) {
                    case 20:
                        return 'vingt';
                    case 30:
                        return 'trente';
                    case 40:
                        return 'quarante';
                    case 50:
                        return 'cinquante';
                    case 60:
                        return 'soixante';
                    case 70:
                        return 'soixante-dix';
                    case 80:
                        return 'quatre-vingt';
                    case 90:
                        return 'quatre-vingt-dix';
                }
            } elseif (substr($number, -1) == 1) {
                if (((int)($number / 10) * 10) < 70) {
                    return $this->convertIntToString((int)($number / 10) * 10) . '-et-un';
                } elseif ($number == 71) {
                    return 'soixante-et-onze';
                } elseif ($number == 81) {
                    return 'quatre-vingt-un';
                } elseif ($number == 91) {
                    return 'quatre-vingt-onze';
                }
            } elseif ($number < 70) {
                return $this->convertIntToString($number - $number % 10) . '-' . $this->convertIntToString($number % 10);
            } elseif ($number < 80) {
                return $this->convertIntToString(60) . '-' . $this->convertIntToString($number % 20);
            } else {
                return $this->convertIntToString(80) . '-' . $this->convertIntToString($number % 20);
            }
        } else if ($number == 100) {
            return 'cent';
        } else if ($number < 200) {
            return $this->convertIntToString(100) . ' ' . $this->convertIntToString($number % 100);
        } else if ($number < 1000) {
            if ($number % 100 == 0)
                return $this->convertIntToString((int)($number / 100)) . ' ' . $this->convertIntToString(100);
            if ($number % 100 != 0) return $this->convertIntToString((int)($number / 100)) . ' ' . $this->convertIntToString(100) . ' ' . $this->convertIntToString($number % 100);
        } else if ($number == 1000) {
            return 'mille';
        } else if ($number < 2000) {
            return $this->convertIntToString(1000) . ' ' . $this->convertIntToString($number % 1000) . ' ';
        } else if ($number < 1000000) {
            return $this->convertIntToString((int)($number / 1000)) . ' ' . $this->convertIntToString(1000) . ' ' . $this->convertIntToString($number % 1000);
        }

        return '';
    }
}
