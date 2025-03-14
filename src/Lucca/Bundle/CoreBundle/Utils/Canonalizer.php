<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Utils;

class Canonalizer
{
    public function slugify(string $string): string
    {
        $string = $this->replaceAccents($string);
        $string = $this->replaceSpecialChars($string);

        return trim(preg_replace('/[^a-z0-9.]+/', '-', strtolower(strip_tags($string))), '-');
    }

    /**
     * replaceSpecialChars
     *
     * Replace all special chars by normal char
     */
    function replaceSpecialChars(string $chaine): string
    {
        $caracteres = ['@' => 'a', '€' => 'e', 'µ' => 'u', 'Œ' => 'oe', 'œ' => 'oe', '$' => 's'];

        $chaine = strtr($chaine, $caracteres);

        return strtolower($chaine);
    }

    /**
     * Replace all special chars by normal char (accents, diacritics, etc.)
     */
    function replaceAccents(string $string): string
    {
        $transformedString = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC')
            ->transliterate($string);

        return strtolower($transformedString);
    }
}

