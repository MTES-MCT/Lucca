<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Manager;

use Lucca\Bundle\SettingBundle\Generator\SettingGenerator;

/**
 * SettingManager retrieve settings whenever you need them in the project, without having to use the container.
 *
 * @see SettingGenerator to declare new settings
 */
abstract class SettingManager
{
    /** Settings array loaded from cache */
    protected static array $settings = [];

    public static function getAll(string $departmentCode): array
    {
        return self::$settings[$departmentCode];
    }

    public static function get(string $departmentCode, string $name, $default = null): mixed
    {
        return self::$settings[$departmentCode][$name] ?? $default;
    }

    public static function setAll(string $departmentCode, array $settings): void
    {
        self::$settings[$departmentCode] = $settings;
    }
}
