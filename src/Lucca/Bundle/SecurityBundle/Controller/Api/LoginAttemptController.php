<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\SecurityBundle\Entity\LoginAttempt;

#[Route(path: '/security/login-attempt')]
#[IsGranted('ROLE_ADMIN')]
class LoginAttemptController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of login attempts
     */
    #[Route(path: '/datatable-search', name: 'lucca_security_api_loginAttempt_datatable_search', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function datatableSearchLoginAttemptAction(Request $request): Response
    {
        /** Get other filters */
        $payload = $request->getPayload()->all();
        $columns = $payload['columns'];
        $order = $payload['order'];

        $orderColumn = $columns[$order[0]['column']]['name'];
        $orderDirection = $order[0]['dir'];

        unset($payload['draw'], $payload['columns'], $payload['order'], $payload['search']);

        ['count' => $count, 'loginAttempts' => $loginAttempts] = $this->em->getRepository(LoginAttempt::class)
            ->findForDatatable($orderColumn, $orderDirection, ...$payload);

        /** Translate family and status, set routes */
        foreach ($loginAttempts as $key => $loginAttempt) {
            $loginAttempts[$key]['showRoute'] = $this->generateUrl('lucca_security_login_attempt_show', ['id' => $loginAttempt['id']]);
            $loginAttempts[$key]['approuveIpRoute'] = $this->generateUrl('lucca_security_login_attempt_approve_ip', ['id' => $loginAttempt['id']]);
        }

        return new JsonResponse([
            'data' => $loginAttempts,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
        ]);

    }
}
