<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Lucca\Bundle\SettingBundle\Manager\SettingManager;

class LuccaSettingBundle extends Bundle
{
    public function __construct(
        private readonly SettingGenerator $settingGenerator,
    )
    {
    }

    /**
     * @ihneritdoc
     */
    public function boot(): void
    {
        $settings = $this->settingGenerator->getCachedSettings(false);

        SettingManager::setAll($settings);
    }
}
