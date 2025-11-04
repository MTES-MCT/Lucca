<?php

namespace Lucca\Bundle\SecurityBundle\Authenticator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Lucca\Bundle\UserBundle\Entity\User;

class RestApiAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        #[Autowire(param: 'lucca_core.lucca_rest_api_key')]
        private readonly string $validApiKey
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->getPathInfo(), '/api/external/');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $header = $request->headers->get('Authorization');
        if (!$header || !str_starts_with(strtolower($header), 'api-key ')) {
            throw new AuthenticationException('Missing API key');
        }

        $apiKey = substr($header, 8);
        if ($apiKey !== $this->validApiKey) {
            throw new AuthenticationException('Invalid API key');
        }

        return new SelfValidatingPassport(
            new UserBadge('rest_api', function ($userIdentifier) {
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $userIdentifier]);
                if (!$user) {
                    throw new AuthenticationException('User '. $userIdentifier . ' not found please create it.');
                }
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        return null; // continue request processing
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse(['error' => $exception->getMessage()], 401);
    }
}
