<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Lucca\Bundle\DepartmentBundle\Entity\Department;

class UserDepartmentResolver
{
    private ?string $luccaUnitTestDepCode;
    private ?string $departmentCode = null;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ParameterBagInterface  $parameterBag,
        private readonly RequestStack           $requestStack,
    )
    {
        $this->luccaUnitTestDepCode = $this->parameterBag->get('lucca_core.lucca_unit_test_dep_code');
    }

    /** Get current department */
    public function getDepartmentCode(): ?string
    {
        /** If in case of unit test get the department by code */
        if ($this->luccaUnitTestDepCode !== 'null') {
            return $this->luccaUnitTestDepCode;
        }

        if ($this->departmentCode) {
            return $this->departmentCode;
        }

        // Retrieve the subdomain from the HTTP request
        $currentRequest = $this->requestStack?->getCurrentRequest();
        $departmentCode = null;
        if ($currentRequest) {
            $hostParts = explode('.', $currentRequest->getHost());
            if (count($hostParts) > 2) {
                $departmentCode = $hostParts[0];
            }
        }

        $this->departmentCode = $departmentCode;

        return $departmentCode;
    }

    public function getDepartment(): ?Department
    {
        $departmentCode = $this->getDepartmentCode();
        if (!$departmentCode) {
            return null;
        }

        return $this->em->getRepository(Department::class)->findOneBy(['code' => $departmentCode]);
    }
}
