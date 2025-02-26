<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Utils;

class Canonalizer
{
    public function slugify(string $string): string
    {
        $string = $this->replaceSpecialChars($string);

        return trim(preg_replace('/[^a-z0-9.]+/', '-', strtolower(strip_tags($string))), '-');
    }

    /**
     * Replace all special chars by normal char (accents, diacritics, etc.)
     */
    function replaceSpecialChars(string $string): string
    {
        $transformedString = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC')
            ->transliterate($string);

        return strtolower($transformedString);
    }

    public function getName(): string
    {
        return 'lucca.core.utils.canonalizer';
    }
}

