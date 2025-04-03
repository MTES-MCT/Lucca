<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceExtension extends AbstractExtension
{
    /**
     * Get twig filters
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter']),
        ];
    }

    public function priceFilter($number, $decimals = 2, $decPoint = ',', $thousandsSep = ' '): string
    {
        $price = $number / 100;

        $price = number_format($price, $decimals, $decPoint, $thousandsSep);

        return $price . ' €';
    }
}
