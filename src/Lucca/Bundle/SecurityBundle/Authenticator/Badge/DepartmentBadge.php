<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Authenticator\Badge;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

class DepartmentBadge implements BadgeInterface
{
    private bool $resolved = false;
    private string $expectedDepartment;

    public function __construct(string $expectedDepartment)
    {
        $this->expectedDepartment = $expectedDepartment;
    }

    public function getExpectedDepartment(): string
    {
        return $this->expectedDepartment;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

    public function markResolved(): void
    {
        $this->resolved = true;
    }
}
