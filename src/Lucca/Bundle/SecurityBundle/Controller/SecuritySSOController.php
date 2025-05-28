<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Controller;

use Lucca\Bundle\SecurityBundle\Service\ProConnectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[Route('/sso')]
class SecuritySSOController extends AbstractController
{
    use TargetPathTrait;

    public function __construct(
        private readonly ProConnectService $proConnectService,
    )
    {
    }

    #[Route('/connect/proconnect', name: 'lucca_security_sso_proconnect_connect', methods: ['GET'])]
    public function connect(): RedirectResponse
    {
        return $this->proConnectService->connect();
    }
}
