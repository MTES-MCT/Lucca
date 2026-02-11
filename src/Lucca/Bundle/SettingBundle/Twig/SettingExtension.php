<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Lucca\Bundle\SettingBundle\Manager\SettingManager;

class SettingExtension extends AbstractExtension
{    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
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
            new TwigFilter('settingMediaUrl', [$this, 'settingMediaUrlFilter']),
        ];
    }

    /**
     * Return a setting value from its name
     */
    public function settingFilter(string $settingName, ?string $depCode = null): mixed
    {
        return SettingManager::get($settingName, null, $depCode);
    }
    public function settingMediaUrlFilter(string $settingName, ?string $default = null, ?string $depCode = null): ?string
    {
        $fileName = SettingManager::get($settingName, null, $depCode);

        if (!$fileName) {
            return null;
        }

        try {
            return $this->urlGenerator->generate('lucca_media_show', [
                'p_fileName' => $fileName,
            ]);
        } catch (ExceptionInterface $e) {
            return null;
        }
    }
}
