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
    private ?Department $department = null;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ParameterBagInterface  $parameterBag,
        private readonly RequestStack           $requestStack,
    )
    {
        $this->luccaUnitTestDepCode = $this->parameterBag->get('lucca_core.lucca_unit_test_dep_code');
    }

    public function getDepartment(): ?Department
    {
        if ($this->department) {
            return $this->department;
        }

        /** If in case of unit test get the department by code */
        if ($this->luccaUnitTestDepCode !== 'null') {
            $this->department = $this->em->getRepository(Department::class)->findOneBy(['code' => $this->luccaUnitTestDepCode]);

            return $this->department;
        }

        $currentRequest = $this->requestStack?->getCurrentRequest();
        $this->department = $this->em->getRepository(Department::class)->findOneBy(['domainName' => $currentRequest?->getHost()]);

        if (!$this->department) {
            return null;
        }

        return $this->department;
    }
}
