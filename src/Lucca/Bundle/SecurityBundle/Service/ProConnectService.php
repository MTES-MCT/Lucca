<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class ProConnectService
{
    public function __construct(
        private RequestStack          $requestStack,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * Generate the ProConnect authentication URL and redirect the user to it.
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
            'client_id'     => '',
            'redirect_uri'  => 'https://admin-lucca.local',
            'scope'         => 'openid email',
            'state'         => $state,
            'nonce'         => $nonce,
        ]);

        return new RedirectResponse($_ENV['PROCONNECT_AUTH_URL'] . '/authorize?' . $query);
    }

    /**
     * À adapter : ici on vérifie juste si l'utilisateur a bien été authentifié par ProConnect.
     */
    public function isConnected(): bool
    {
        $session = $this->requestStack->getSession();
        return $session->has('proconnect_user');
    }
}
