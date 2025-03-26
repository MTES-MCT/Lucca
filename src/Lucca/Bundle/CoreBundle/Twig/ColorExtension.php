<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Lucca\Bundle\DecisionBundle\Entity\Commission;

class ColorExtension extends AbstractExtension
{
    /**
     * Get twig filters
     * Add is_safe param to render HTML in filter
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('colorDecision', [$this, 'colorDecision'], ['is_safe' => ['html']]),
            new TwigFilter('colorTypeDecision', [$this, 'colorTypeDecision'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Return a color depend on decision ()
     * return an hexadecimal color like #FFFFFF
     */
    public function colorDecision($value): string
    {
        return match ($value) {
            "countDemolition" => '#F3D5C0',
            "countPenalties" => '#D4B499',
            "countContradictories" => '#889EAF',
            "countExpulsion" => '#506D84',
            default => '#CEE5D0',
        };
    }

    /**
     * Return a color depend on type of decision
     * return an hexadecimal color like #FFFFFF
     */
    public function colorTypeDecision($value): string
    {
        return match ($value) {
            Commission::STATUS_GUILTY => '#92817A',
            Commission::STATUS_GUILTY_EXEMPT => '#BEDBBB',
            Commission::STATUS_GUILTY_RESTITUTION => '#8DB596',
            Commission::STATUS_RELAXED => '#707070',
            default => '#FAFCC2',
        };
    }
}
