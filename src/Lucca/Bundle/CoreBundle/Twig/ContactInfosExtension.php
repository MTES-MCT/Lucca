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

class ContactInfosExtension extends AbstractExtension
{
    /**
     * Get twig filters
     * Add is_safe param to render HTML in filter
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('contact_infos', [$this, 'getContactInfos'], ['is_safe' => ['html']]),
            new TwigFilter('contact_help_control', [$this, 'getContactHelpControl'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Get contact infos depends on the department of the lucca instance
     *
     * @param $p_department
     * @return string[]
     */
    public function getContactInfos($p_department): array
    {
        return match (intval($p_department)) {
            66 => [
                [
                    'mailto' => 'ddtm-cabanisation@pyrenees-orientales.gouv.fr',
                    'phone' => '04 68 38 12 34',
                    'addr' => '2 Rue Jean Richepin,<br>66000 Perpignan,<br>France',
                ],
            ],
            34 => [
                [
                    'mailto' => 'ddtm@herault.gouv.fr',
                    'phone' => '04 34 46 60 91',
                    'addr' => 'DDTM 34,<br>Bâtiment Ozone,<br>181 place Ernest Granier,<br>CS 60556,<br>34064 Montpellier Cedex,<br>France',
                ],
                [
                    'mailto' => 'ddtm@herault.gouv.fr', 'phone' => '04 67 11 10 00',
                    'addr' => 'DDTM 34,<br>Impasse Barrière Joseph, <br>34521 Béziers, <br>France',
                ],
            ],
            12 => [
                [
                    'mailto' => 'ddt-cabanisation@aveyron.gouv.fr',
                    'phone' => '05 65 75 49 59',
                    'addr' => '9 rue de Bruxelles,<br>ZAC de Bourran BP 3370,<br>12033 Rodez Cedex 9,<br>France',
                ],
            ],
            31 => [
                [
                    'mailto' => 'ddt-cabanisation@haute-garonne.gouv.fr',
                    'phone' => '05 81 97 71 00',
                    'addr' => '2 Bd Armand Duportal BP 70001,<br>31074 Toulouse CEDEX 9,<br>France',
                ],
            ],
            33 => [
                [
                    'mailto' => 'ddtm-supem-afpu@gironde.gouv.fr',
                    'phone' => '05 47 30 51 90',
                    'addr' => 'Cordonnées de la DDTM 33<br>Service Urbanisme, Paysage, Energies et Mobilités<br>',
                ],
            ],
            default => [],
        };
    }

    /**
     * Get contact infos to help to readact the control
     */
    public function getContactHelpControl($p_department): array
    {
        return match (intval($p_department)) {
            66 => $this->getContactInfos(66),
            34 => [
                [
                    'mailto' => 'ddtm@herault.gouv.fr',
                    'phone' => '04 34 46 61 88',
                    'addr' => 'DDTM 34,<br>Impasse Barrière Joseph, <br>34521 Béziers, <br>France',
                ],
            ],
            12 => $this->getContactInfos(12),
            31 => $this->getContactInfos(31),
            33 => $this->getContactInfos(33),
            default => [],
        };
    }
}
