<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Lucca\Bundle\SettingBundle\Generator\SettingGenerator;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

readonly class SettingsListener
{
    /**
     * RequestListener constructor.
     */
    public function __construct(
        private SettingGenerator       $settingGenerator,
        private UserDepartmentResolver $userDepartmentResolver,
    )
    {
    }

    /**
     * Call on every kernel request
     * Check LoginAttempts made with Ip on request
     */
    #[AsEventListener(event: 'kernel.request')]
    public function onKernelRequest(): void
    {
        $departmentCode = $this->userDepartmentResolver->getDepartmentCode();
        $settings = $this->settingGenerator->getCachedSettings($departmentCode);

        SettingManager::setAll($settings);
    }
}
