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

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\DepartmentBundle\Exception\DepartmentNotFoundException;
use Lucca\Bundle\DepartmentBundle\Exception\UnauthorizedDepartmentAccessException;
use Lucca\Bundle\UserBundle\Entity\User;

class UserDepartmentResolver
{
    private ?string $luccaUnitTestDepCode;
    private ?Department $department = null;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ParameterBagInterface  $parameterBag,
        private readonly RequestStack           $requestStack,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RouterInterface $router
    )
    {
        $this->luccaUnitTestDepCode = $this->parameterBag->get('lucca_core.lucca_unit_test_dep_code');
    }

    public function getDepartment(): ?Department
    {
        //init context
        $this->getDepartmentContext();
        return $this->department;
    }

    public function getCode(bool $fromSessionLogin = false) : ?string
    {
        //if from session only, we don't need to init the context
        if ($fromSessionLogin) {
            $sessionDepartmentCode = $this->requestStack?->getMainRequest()?->getSession()?->get('department_code_from_login');

            //check if code exists in department repository
            if ($sessionDepartmentCode && $this->em->getRepository(Department::class)->findOneBy(['code' => $sessionDepartmentCode, 'enabled' => true])) {
                return $sessionDepartmentCode;
            }

            return null;
        }

        //init context
        $this->getDepartmentContext();
        return $this->getDepartment()?->getCode();
    }


    private function getDepartmentCodeFromUrl(): ?string
    {
        $currentRequest = $this->requestStack?->getMainRequest();
        $pathInfo = $currentRequest?->getPathInfo();

        if ($pathInfo && preg_match('/^\/([a-zA-Z0-9_-]+)\/.*/', $pathInfo, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function getDepartmentContext(bool $fromSessionOnly = false): void
    {
        if ($this->department) {
            return;
        }

        /** If in case of unit test get the department by code */
        if ($this->luccaUnitTestDepCode !== 'null') {
            $this->department = $this->em->getRepository(Department::class)->findOneBy(['code' => $this->luccaUnitTestDepCode]);
            return;
        }

        $currentRequest = $this->requestStack?->getMainRequest();



        /** First step: check if the request is a Lucca request */
        // If no path info, we cannot determine the department to avoid errors on dev profiler for example
        if ($currentRequest && !str_starts_with($currentRequest->get('_route'), 'lucca_')) {
            return;
        }

        //check if route needs department code
        $route = $this->router->getRouteCollection()->get($currentRequest->get('_route'));


        $routeNeedDepCode = str_contains($route?->getPath() ?? "", '{dep_code}');


        if (!$routeNeedDepCode) {
            return;
        }
        $departmentCode = $this->getDepartmentCodeFromUrl();

        /** Second step: check if the department code is set in the request */
        if ($departmentCode) {
            $sessionDepartmentCode = $currentRequest?->getSession()?->get('user_department_code');
            $this->department = $this->em->getRepository(Department::class)->findOneBy(['code' => $departmentCode, 'enabled' => true]);

            //if department code is same of the session return the department from session to optimize the request
            if ($sessionDepartmentCode === $departmentCode) {
                return;
            }

            if ($currentRequest->get('_route') === 'lucca_user_security_login') {
                return;
            }

            $user = $this->tokenStorage->getToken()?->getUser();

            //check if the user use the department
            if ($user instanceof User && $this->department && !$user->getDepartments()->contains($this->department)) {
                $depCode = $this->department?->getCode() ?? 'null';
                throw new UnauthorizedDepartmentAccessException(
                    "User {$user->getUsername()} does not have access to department {$depCode}."
                );
            }

            if (!$this->department) {
                throw new DepartmentNotFoundException("Department not found: $departmentCode");
            }
        }

    }
}
