<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Controller\Sso;

use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Lucca\Bundle\SecurityBundle\Service\ProConnectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecuritySSOController extends AbstractController
{
    use TargetPathTrait;

    public function __construct(
        private readonly ProConnectService $proConnectService,
        private readonly UserDepartmentResolver $userDepartmentResolver,
    )
    {
    }

    #[Route('/connect/proconnect', name: 'lucca_security_sso_proconnect_connect', methods: ['GET'])]
    public function connect(): RedirectResponse
    {
        try {
            return $this->proConnectService->connect();
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Error connecting to ProConnect: ' . $e->getMessage());
            return $this->redirectToRoute('lucca_user_security_login', [
                'dep_code' => $this->userDepartmentResolver->getCode(true)
            ]);
        }
    }

    #[Route('/connect/proconnect/check', name: 'lucca_security_connect_proconnect_check', methods: ['GET'])]
    public function check(): Response
    {
        try {
            return $this->proConnectService->check();
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Error connecting to ProConnect: ' . $e->getMessage());
            return $this->redirectToRoute('lucca_user_security_login', [
                'dep_code' => $this->userDepartmentResolver->getCode(true)
            ]);
        }
    }
}
