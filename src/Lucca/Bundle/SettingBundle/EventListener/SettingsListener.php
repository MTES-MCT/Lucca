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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;

use Lucca\Bundle\SettingBundle\Generator\SettingGenerator;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

readonly class SettingsListener
{
    /**
     * RequestListener constructor.
     */
    public function __construct(
        private SettingGenerator $settingGenerator,
        private RequestStack     $requestStack,
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
        // Retrieve the subdomain from the HTTP request
        $currentRequest = $this->requestStack->getCurrentRequest();
        $departmentCode = null;
        if ($currentRequest) {
            $hostParts = explode('.', $currentRequest->getHost());
            if (count($hostParts) > 2) {
                $departmentCode = $hostParts[0];
            }
        }

        if (null === $departmentCode) {
            $departmentCode = 'demo';
        }

        $settings = $this->settingGenerator->getCachedSettings($departmentCode);

        SettingManager::setAll($departmentCode, $settings);
    }
}
