<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Manager;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
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

    protected static ?Department $department = null;

    public function __construct(
        private readonly UserDepartmentResolver $userDepartmentResolver,
    )
    {
        self::$department = $this->userDepartmentResolver->getDepartment();
    }

    public static function getAll(): array
    {
        return self::$settings[self::$department?->getCode()];
    }

    public static function get(string $name, $default = null): mixed
    {
        return self::$settings[self::$department?->getCode()][$name] ?? $default;
    }

    public static function setAll(array $settings): void
    {
        self::$settings[self::$department?->getCode()] = $settings;
    }
}
