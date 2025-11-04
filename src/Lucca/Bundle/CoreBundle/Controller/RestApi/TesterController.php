<?php

namespace Lucca\Bundle\CoreBundle\Controller\RestApi;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route(path: '/')]
class TesterController extends AbstractController
{
    #[Route(path: '/test', name: 'lucca_core_rest_api_tester', methods: ['GET'])]
    public function testConnexion(): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'message' => 'Successful connection to the REST API',
            'data' => [
                'timestamp' => (new \DateTime())->format(DATE_ATOM),
            ]
        ]);
    }
}
