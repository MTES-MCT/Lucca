<?php

namespace Lucca\Bundle\SecurityBundle\Authenticator;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;
use Psr\Log\LoggerInterface;

class RestApiAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Configuration $jwtConfig,
        private readonly LoggerInterface $logger,
    ) {}

    public function supports(Request $request): ?bool
    {
        // Sécurise uniquement les endpoints /api/external/ sauf /get-token
        return str_starts_with($request->getPathInfo(), '/api/external/')
            && !str_starts_with($request->getPathInfo(), '/api/external/get-token');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $header = $request->headers->get('Authorization');
        $clientIp = $request->getClientIp();

        if (!$header || !str_starts_with(strtolower($header), 'bearer ')) {
            $this->logger->warning('Missing JWT token', ['ip' => $clientIp]);
            throw new AuthenticationException('Missing JWT token');
        }

        $tokenString = substr($header, 7);

        try {
            $token = $this->jwtConfig->parser()->parse($tokenString);
            assert($token instanceof UnencryptedToken);
        } catch (\Throwable $e) {
            $this->logger->warning('Invalid JWT token (parsing failed)', ['ip' => $clientIp]);
            throw new AuthenticationException('Invalid JWT token');
        }

        // Contrainte signature + validité
        $constraints = [
            new SignedWith($this->jwtConfig->signer(), $this->jwtConfig->signingKey()),
            new ValidAt(new SystemClock(new \DateTimeZone('UTC'))),
        ];

        if (!$this->jwtConfig->validator()->validate($token, ...$constraints)) {
            $this->logger->warning('JWT token expired or invalid', ['ip' => $clientIp]);
            throw new AuthenticationException('JWT token expired or invalid');
        }

        $userIdentifier = $token->claims()->get('sub');
        if (!$userIdentifier) {
            $this->logger->warning('JWT token missing "sub" claim', ['ip' => $clientIp]);
            throw new AuthenticationException('JWT token missing "sub" claim');
        }

        return new SelfValidatingPassport(
            new UserBadge($userIdentifier, function ($userIdentifier) use ($clientIp) {
                $user = $this->entityManager->getRepository(User::class)
                    ->findOneBy(['username' => $userIdentifier]);
                if (!$user) {
                    $this->logger->warning('User not found for JWT token', ['user' => $userIdentifier]);
                    throw new AuthenticationException('User ' . $userIdentifier . ' not found.');
                }
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        return null; // Continue request processing
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse(['error' => $exception->getMessage()], 401);
    }
}
