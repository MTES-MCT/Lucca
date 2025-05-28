<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Authenticator;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class ProconnectAuthenticator extends AbstractAuthenticator
{
    private SessionInterface $session;

    public function __construct(
        private readonly HttpClientInterface    $httpClient,
        private readonly UrlGeneratorInterface  $urlGenerator,
        private readonly UserProviderInterface  $userProvider,
        private readonly RequestStack           $requestStack,
        private readonly EntityManagerInterface $em,
        private readonly ParameterBagInterface  $parameterBag,

        /** Define constant to name route called after a login */
        #[Autowire(param: 'lucca_security.default_url_after_login')]
        private readonly string                 $routeAfterLogin,
        #[Autowire(param: 'lucca_security.default_admin_url_after_login')]
        private readonly string                 $adminRouteAfterLogin,
    )
    {
        $this->session = $this->requestStack->getSession();
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_proconnect_check' && $request->query->has('code');
    }

    public function authenticate(Request $request): Passport
    {
        $code = $request->query->get('code');
        $state = $request->query->get('state');

        if ($state !== $this->session->get('oidc_state')) {
            throw new AuthenticationException('Invalid OIDC state.');
        }

        $response = $this->httpClient->request('POST', $_ENV['PROCONNECT_AUTH_URL'] . '/token', [
            'body' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->urlGenerator->generate('connect_proconnect_check', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'client_id' => $_ENV['PROCONNECT_CLIENT_ID'],
                'client_secret' => $_ENV['PROCONNECT_CLIENT_SECRET'],
            ],
        ]);

        $data = $response->toArray(false);
        $idToken = $data['id_token'] ?? null;

        if (!$idToken) {
            throw new AuthenticationException('No ID token returned.');
        }

        $jwtPayload = json_decode(base64_decode(explode('.', $idToken)[1] ?? ''), true);

        if (!isset($jwtPayload['email'])) {
            throw new AuthenticationException('Email not found in ID token.');
        }

        // Verify the nonce
        if ($jwtPayload['nonce'] !== $this->session->get('oidc_nonce')) {
            throw new AuthenticationException('Invalid nonce.');
        }

        $email = $jwtPayload['email'];

        // Store user info in session for later use
        $this->session->set('proconnect_user', [
            'email' => $email,
            'payload' => $jwtPayload,
        ]);

        return new SelfValidatingPassport(
            new UserBadge($email, fn() => $this->userProvider->loadUserByIdentifier($email))
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();
        $user->setLastLogin(new \DateTime('now'));

        $this->em->persist($user);
        $this->em->flush();

        // Storage of the last username in the request attributes
        $request->attributes->set(SecurityRequestAttributes::LAST_USERNAME, $user->getEmail());

        // Storage of the last username in the session
        $session = $request->getSession();
        $session->set(SecurityRequestAttributes::LAST_USERNAME, $user->getEmail());

        if ($this->parameterBag->get('lucca_core.admin_domain_name') === $request->headers->get('host')) {
            return new RedirectResponse($this->urlGenerator->generate($this->adminRouteAfterLogin));
        }

        return new RedirectResponse($this->urlGenerator->generate($this->routeAfterLogin));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Authentication Failed: ' . $exception->getMessage(), Response::HTTP_FORBIDDEN);
    }
}
