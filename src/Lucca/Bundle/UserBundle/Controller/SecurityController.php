<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Display a login form to authenticate an anonymous user
     */
    #[Route(path: '/login', name: 'lucca_user_security_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        /** get the login error if there is one */
        $error = $authenticationUtils->getLastAuthenticationError();

        /** last username entered by the user */
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@LuccaUser/Security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error
        ));
    }

    /**
     * Symfony route to permit logout action
     */
    #[Route(path: '/logout', name: 'lucca_user_security_logout', methods: ['GET'])]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
