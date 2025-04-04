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

class BooleanExtension extends AbstractExtension
{
    /**
     * Get twig filters
     * Add is_safe param to render HTML in filter
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('boolean', [$this, 'booleanFilter'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Format percent number with params
     */
    public function booleanFilter($value): string
    {
        if (!is_bool($value)) {
            return 'The value is not a valid boolean';
        }

        if ($value) {
            return '<i class="fa fa-check text-success"></i>';
        }

        return '<i class="fa fa-times text-danger"></i>';
    }
}
