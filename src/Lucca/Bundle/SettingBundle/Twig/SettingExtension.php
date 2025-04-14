<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Lucca\Bundle\SettingBundle\Manager\SettingManager;

class SettingExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
    )
    {
    }

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
    public function settingFilter(string $settingName): mixed
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

        return SettingManager::get($departmentCode, $settingName);
    }
}
