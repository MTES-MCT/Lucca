<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/')]
class IpBanController extends AbstractController
{
    /**
     * IpBan
     */
    #[Route(path: '/your-ip-is-banned', name: 'lucca_security_ip_banned', methods: ['GET'])]
    public function ipBanAction(): Response
    {
        $maxLoginAttempts = $this->getParameter('lucca_security.protection')['max_login_attempts'];

        return $this->render('@LuccaSecurity/Security/ipBan.html.twig', array(
            'maxLoginAttempts' => $maxLoginAttempts,
        ));
    }
}

