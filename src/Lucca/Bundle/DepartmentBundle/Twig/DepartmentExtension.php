<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Twig;

use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class DepartmentExtension
 */
class DepartmentExtension extends AbstractExtension
{
    public function __construct(
        private readonly UserDepartmentResolver $userDepartmentResolver,
    ) {}

    /**
     * Register custom Twig functions
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getDepartmentCode', [$this, 'getCode']),
        ];
    }

    /**
     * Get the department code of the current user
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->userDepartmentResolver->getCode();
    }
}
