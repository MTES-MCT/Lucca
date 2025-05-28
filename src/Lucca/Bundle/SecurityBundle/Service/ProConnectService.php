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
use Random\RandomException;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\{RedirectResponse, RequestStack, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\SecurityBundle\Authenticator\ProconnectAuthenticator;
use Lucca\Bundle\UserBundle\Entity\User;

readonly class ProConnectService
{
    private string $redirectUri;
    public function __construct(
        private RequestStack               $requestStack,
        private UrlGeneratorInterface      $urlGenerator,
        private UserAuthenticatorInterface $userAuthenticator,
        private EntityManagerInterface     $em,
        private ProConnectAuthenticator    $proConnectAuthenticator,
        private TranslatorInterface        $translator,
        private string $proconnectAuthUrl,
        private string $proconnectClientId,
        private string $proconnectClientSecret,
    )
    {
        $this->redirectUri = $this->urlGenerator->generate('lucca_security_connect_proconnect_check', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Generate the ProConnect authentication URL and redirect the user to it.
     * @throws RandomException
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

        return new RedirectResponse($this->proconnectAuthUrl . '/authorize?' . $query);
    }

    /**
     * Check the ProConnect authentication status and redirect accordingly.
     * @throws TransportExceptionInterface
     */
    public function check(): Response
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $this->requestStack->getSession();

        $code = $request->query->get('code');
        $state = $request->query->get('state');

        if (!$code || !$state || $state !== $session->get('oidc_state')) {
            $this->translator->trans('proconnect.error.token_not_found', [], 'SecurityBundle');
            return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
        }

        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', $this->proconnectAuthUrl . '/token', [
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'body' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->proconnectClientId,
                'client_secret' => $this->proconnectClientSecret,
            ],
        ]);

        $data = $response->toArray(false);

        if (!isset($data['id_token'])) {
            $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.token_not_found', [], 'SecurityBundle'));
            return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
        }

//        $jwt = explode('.', $data['id_token']);
//        $payload = json_decode(base64_decode(strtr($jwt[1], '-_', '+/')), true);
        $accessToken = $data['access_token'] ?? null;

        if (!$accessToken) {
            $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.access_token_missing', [], 'SecurityBundle'));
            return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
        }

        $response = $httpClient->request('GET', $this->proconnectAuthUrl . '/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);
        $jwt = $response->getContent();

        // Split the JWT into its components
        [$header, $payload, $signature] = explode('.', $jwt);

        //TODO : Verify the signature here if needed

        // decode the payload
        $decodedPayload = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        $email = $decodedPayload['email'] ?? null;

        if (!$email) {
            $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.email_not_found', [], 'SecurityBundle'));
            return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            $session->getFlashBag()->add('danger', $this->translator->trans('proconnect.error.user_not_found', ['%email%' => $email], 'SecurityBundle'));
            return new RedirectResponse($this->urlGenerator->generate('lucca_user_security_login'));
        }

        $request = $this->requestStack->getCurrentRequest();
        return $this->userAuthenticator->authenticateUser($user, $this->proConnectAuthenticator, $request);
    }
}
