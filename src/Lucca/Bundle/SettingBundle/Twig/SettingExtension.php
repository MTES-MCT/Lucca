<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Lucca\Bundle\SettingBundle\Manager\SettingManager;

class SettingExtension extends AbstractExtension
{
    /**
     * Get twig filters
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('setting', [$this, 'settingFilter']),
        ];
    }

    /**
     * Return a setting value from its name
     */
    public function settingFilter(string $settingName, ?string $depCode = null): mixed
    {
        return SettingManager::get($settingName, null, $depCode);
    }
}
