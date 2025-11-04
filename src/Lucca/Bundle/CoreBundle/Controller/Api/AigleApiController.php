<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Controller\Api;

use Lucca\Bundle\CoreBundle\Service\AigleApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


#[Route(path: '/api/aigle')]
#[IsGranted('ROLE_ADMIN')]
class AigleApiController extends AbstractController
{
    public function __construct(
        private readonly AigleApiClient $aigleApiClient,
    )
    {
    }

    /**
     * List of LoginAttempt
     */
    #[Route(path: '/test', name: 'lucca_core_api_aigle_test_connection', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function testConnexion(): JsonResponse
    {
        try {
            $response = $this->aigleApiClient->get('/test');
            $statusCode = $response->getStatusCode();
            $content = $response->getContent();
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'status' => 'error',
                'content' => null,
            ], Response::HTTP_BAD_GATEWAY);
        }

        return new JsonResponse([
            'status' => $statusCode,
            'content' => $content,
        ]);
    }
}
