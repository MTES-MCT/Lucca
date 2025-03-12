<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

use Lucca\Bundle\SettingBundle\Generator\SettingGenerator;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

readonly class SettingsListener
{
    /**
     * RequestListener constructor.
     */
    function __construct(
        private SettingGenerator $settingGenerator,
    )
    {
    }

    /**
     * Call on every kernel request
     * Check LoginAttempts made with Ip on request
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $settings = $this->settingGenerator->getCachedSettings();

        SettingManager::setAll($settings);
    }
}
