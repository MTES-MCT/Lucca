<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Controller;

use Lucca\Bundle\SecurityBundle\Service\ProConnectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;

class SecurityController extends AbstractController
{
    use TargetPathTrait;

    public function __construct(
        private readonly UserDepartmentResolver $userDepartmentResolver,
        private readonly ProConnectService $proConnectService,
    )
    {
    }

    /**
     * Display a login form to authenticate an anonymous user
     */
    #[Route(path: '/login', name: 'lucca_user_security_login', methods: ['GET', 'POST'])]
    public function login(
        #[CurrentUser] ?User $user,
        Request $request,
        AuthenticationUtils $helper,
    ): Response
    {
        /** Get default node sor security protection */
        $routeAfterLogin = $this->getParameter('lucca_security.default_url_after_login');

        //set department code in session
        $request->getSession()->set('department_code_from_login', $this->userDepartmentResolver->getCode());

        $isAdminDepartment = $this->userDepartmentResolver->getCode() === 'admin';

        // if user is already logged in, don't display the login page again
        if ($isAdminDepartment) {
            $routeAfterLogin = $this->getParameter('lucca_security.default_admin_url_after_login');
        }

        if($isAdminDepartment){
            return $this->render('@LuccaUser/Security/badDepartment.html.twig');
        }

        if ($user) {
            return $this->redirectToRoute($routeAfterLogin);
        }

        // this statement solves an edge-case: if you change the locale in the login
        // page, after a successful login you are redirected to a page in the previous
        // locale. This code regenerates the referrer URL whenever the login page is
        // browsed, to ensure that its locale is always the current one.
//        $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl($routeAfterLogin));

        return $this->render('@LuccaUser/Security/login.html.twig', array(
            // last username entered by the user (if any)
            'last_username' => $helper->getLastUsername(),
            // last authentication error (if any)
            'error' => $helper->getLastAuthenticationError(),
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
