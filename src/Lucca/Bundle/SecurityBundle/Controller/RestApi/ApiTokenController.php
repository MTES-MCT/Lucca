<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Controller\RestApi;

use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lucca\Bundle\UserBundle\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
    Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route(path: '/')]
class ApiTokenController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface   $entityManager,
        private readonly LoggerInterface          $logger,
        private readonly Configuration $jwtConfig,
        #[Autowire(param: 'lucca_security.lucca_rest_api_key')]
        private readonly string                   $validApiKey,
        #[Autowire(param: 'lucca_security.lucca_rest_api_username')]
        private readonly string                   $username,
        #[Autowire(param: 'lucca_security.lucca_rest_api_jwt_expiration')]
        private readonly int                      $expiresIn,
    )
    {
    }

    /**
     * IpBan
     */
    #[Route(path: '/get-token', name: 'lucca_security_rest_api_get_token', methods: ['GET'])]
    public function getToken(Request $request): JsonResponse
    {
        {
            $header = $request->headers->get('Authorization');
            $clientIp = $request->getClientIp();

            if (!$header || !str_starts_with(strtolower($header), 'api-key ')) {
                $this->logger->warning('Missing API key', ['ip' => $clientIp]);
                return new JsonResponse(['error' => 'Missing API key'], 401);
            }

            $apiKey = substr($header, 8);
            if ($apiKey !== $this->validApiKey) {
                $this->logger->warning('Invalid API key attempt', [
                    'ip' => $clientIp,
                    'provided_key' => $apiKey
                ]);
                return new JsonResponse(['error' => 'Invalid API key'], 401);
            }

            // Retrieve the user
            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['username' => $this->username]);
            if (!$user) {
                $this->logger->error('Rest API user not found', ['username' => $this->username]);
                return new JsonResponse(['error' => 'User ' . $this->username . ' not found, please create it.'], 500);
            }

            // Generate JWT token
            $token = $this->jwtConfig->builder()
                ->issuedAt(new \DateTimeImmutable())
                ->expiresAt((new \DateTimeImmutable())->modify('+' . $this->expiresIn . ' seconds'))
                ->relatedTo($this->username)
                ->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey());

            $this->logger->info('JWT token generated', ['username' => $this->username, 'ip' => $clientIp]);

            return new JsonResponse([
                'token' => $token->toString(),
                'expires_in' => $this->expiresIn
            ]);
        }
    }
}

