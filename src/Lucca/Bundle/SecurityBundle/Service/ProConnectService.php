<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\{RedirectResponse, RequestStack, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

use Lucca\Bundle\SecurityBundle\Authenticator\ProconnectAuthenticator;
use Lucca\Bundle\UserBundle\Entity\User;

readonly class ProConnectService
{
    private string $redirectUri;
    private ?array $openIdConf;
    private HttpClientInterface $httpClient;

    /**
     * Service constructor. It loads the OpenID configuration from the discovery endpoint.
     * It also initializes the redirect URI dynamically.
     */
    public function __construct(
        private RequestStack               $requestStack,
        private UrlGeneratorInterface      $urlGenerator,
        private UserAuthenticatorInterface $userAuthenticator,
        private EntityManagerInterface     $em,
        private ProConnectAuthenticator    $proConnectAuthenticator,
        private TranslatorInterface        $translator,
        #[Autowire(param: 'lucca_security.proconnect_auth_url')]
        private string                     $proconnectAuthUrl,
        #[Autowire(param: 'lucca_security.proconnect_client_id')]
        private string                     $proconnectClientId,
        #[Autowire(param: 'lucca_security.proconnect_client_secret')]
        private string                     $proconnectClientSecret,
    )
    {
        $this->httpClient = HttpClient::create();

        // Retrieve OpenID Connect configuration (discovery document)
        $response = $this->httpClient->request('GET', $this->proconnectAuthUrl . '/.well-known/openid-configuration');
        $this->openIdConf = $response->toArray() ?? null;

        // Generate absolute redirect URI for the callback route
        $this->redirectUri = $this->urlGenerator->generate('lucca_security_connect_proconnect_check', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Initiates the authentication process by redirecting the user to ProConnect.
     * It sets a `state` and `nonce` in the session for later validation.
     */
    public function connect(): RedirectResponse
    {
        $state = bin2hex(random_bytes(16));
        $nonce = bin2hex(random_bytes(16));

        $session = $this->requestStack->getSession();
        $session->set('oidc_state', $state);
        $session->set('oidc_nonce', $nonce);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $this->proconnectClientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'openid email',
            'state' => $state,
            'nonce' => $nonce,
        ]);

        $authorizationEndpoint = $this->openIdConf['authorization_endpoint'] ?? null;

        if (!$authorizationEndpoint) {
            throw new \RuntimeException('Authorization endpoint not found in OpenID configuration.');
        }

        // Redirect the user to the ProConnect login page
        return new RedirectResponse($authorizationEndpoint . '?' . $query);
    }

    /**
     * Handles the callback from ProConnect after authentication.
     * Validates tokens and logs in the user if successful.
     */
    public function check(): Response
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $this->requestStack->getSession();

        try {
            // Get the authorization code and state from query parameters
            $code = $request->query->get('code');
            $state = $request->query->get('state');

            // Validate the state value to prevent CSRF attacks
            if (!$code || !$state || $state !== $session->get('oidc_state')) {
                $this->translator->trans('proconnect.error.token_not_found', [], 'SecurityBundle');
                return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
            }

            // Exchange the authorization code for tokens
            $tokenEndpoint = $this->openIdConf['token_endpoint'] ?? null;
            $response = $this->httpClient->request('POST', $tokenEndpoint, [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'body' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                    'client_id' => $this->proconnectClientId,
                    'client_secret' => $this->proconnectClientSecret,
                ],
            ]);
            $tokenRaw = $response->toArray(false);

            // Validate presence of ID token
            if (!isset($tokenRaw['id_token'])) {
                $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.token_not_found', [], 'SecurityBundle'));
                return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
            }

            // Get access token to query userinfo endpoint
            $accessToken = $tokenRaw['access_token'] ?? null;
            if (!$accessToken) {
                $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.access_token_missing', [], 'SecurityBundle'));
                return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
            }

            $userInfoEndpoint = $this->openIdConf['userinfo_endpoint'] ?? null;
            $keysUri = $this->openIdConf['jwks_uri'] ?? null;

            try {
                // Get public keys for verifying the JWT
                $response = $this->httpClient->request('GET', $keysUri);
                $keys = $response->toArray();
                $keySet = JWK::parseKeySet($keys);

                // Get the JWT from userinfo endpoint
                $response = $this->httpClient->request('GET', $userInfoEndpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);
                $jwt = $response->getContent();

                // Decode and validate the JWT using public key
                $decoded = (array)JWT::decode($jwt, $keySet);
            } catch (\Exception $e) {
                $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.invalid_signature', [], 'SecurityBundle'));
                return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
            }

            // Extract email from JWT claims
            $email = $decoded['email'] ?? null;

            if (!$email) {
                $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.email_not_found', [], 'SecurityBundle'));
                return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
            }

            // Try to retrieve the user from database by email
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
            if (!$user) {
                $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.user_not_found', ['%email%' => $email], 'SecurityBundle'));
                return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
            }

        } catch (HttpExceptionInterface) {
            $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.http_exception', [], 'SecurityBundle'));
            return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
        }

        // Authenticate and log the user in
        return $this->userAuthenticator->authenticateUser($user, $this->proConnectAuthenticator, $request);
    }
}
