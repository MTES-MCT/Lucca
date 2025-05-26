<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Authenticator;

use Doctrine\ORM\EntityManagerInterface,
    Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Session\SessionInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Http\SecurityRequestAttributes,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge,
    Symfony\Component\Security\Http\Authenticator\Passport\Passport,
    Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator,
    Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials,
    Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken,
    Symfony\Component\Security\Http\Util\TargetPathTrait,
    Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Http\RateLimiter\DefaultLoginRateLimiter;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Lucca\Bundle\SecurityBundle\Authenticator\Badge\DepartmentBadge;
use Lucca\Bundle\SecurityBundle\Manager\LoginAttemptManager;
use Lucca\Bundle\SecurityBundle\Exception\LoginWithoutSessionException;
use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\SecurityBundle\Authenticator\Badge\SuperAdminBadge;
use Lucca\Bundle\SecurityBundle\Entity\LoginAttempt;

/**
 * Class SimpleAuthenticator
 * Manage login attempt - Success / Failure
 *
 * @see https://symfony.com/doc/current/security/guard_authentication.html
 */
class SimpleAuthenticator extends AbstractLoginFormAuthenticator
{
    /**
     * Trait to get (and set) the URL the user last visited before being forced to authenticate.
     */
    use TargetPathTrait;

    /** @var string Define constant to name login route */
    private string $routeLogin = 'lucca_user_security_login';

    /**
     * SimpleAuthenticator constructor
     */
    public function __construct(
        private readonly EntityManagerInterface      $em,
        private readonly UrlGeneratorInterface       $urlGenerator,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly LoginAttemptManager         $loginAttemptManager,
        private readonly AdherentFinder              $adherentFinder,
        private readonly UserDepartmentResolver      $userDepartmentResolver,
        private readonly ParameterBagInterface       $parameterBag,
        private readonly DefaultLoginRateLimiter     $rateLimiter,

        /** Define constant to name route called after a login */
        #[Autowire(param: 'lucca_security.default_url_after_login')]
        private readonly string                      $routeAfterLogin,
        #[Autowire(param: 'lucca_security.default_admin_url_after_login')]
        private readonly string                      $adminRouteAfterLogin,
    )
    {
    }

    /**
     * Does the authenticator support the given Request?
     */
    public function supports(Request $request): bool
    {
        return $this->routeLogin === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $identifier = $request->request->get('username');
        $password = $request->request->get('password');

        /** Check if this request can manage a session */
        if (!$request->hasSession() || !$request->getSession() instanceof SessionInterface) {
            throw new LoginWithoutSessionException("Request cannot have session - fix this if you want to perform login action");
        }

        $user = $this->em->getRepository(User::class)->loadUserByIdentifier($identifier);

        // Don't use the department badge if the request is from the admin domain
        if ($this->parameterBag->get('lucca_core.admin_domain_name') === $request->headers->get('host')) {
            $superAdminBadge = new SuperAdminBadge();
            if ($user?->hasRole('ROLE_SUPER_ADMIN')) {
                $superAdminBadge->markResolved(); // mark the badge as resolved if the user is a super admin
            }

            return new Passport(
                new UserBadge($identifier),
                new PasswordCredentials($password),
                [$superAdminBadge]
            );
        }

        // Check if the user is part of the department
        $department = $this->userDepartmentResolver->getDepartment();
        $departmentBadge = new DepartmentBadge($department);

        if ($user?->getDepartments()->contains($department)) {
            $departmentBadge->markResolved(); // mark the badge as resolved if the user is part of the department
        }

        /**
         * Create a new Passport to transport these data
         *
         * Badge for User -> find the User with the method loadUserByIdentifier
         * Badge for Password -> check the password
         * Badge for CsrfTokenBadge -> check the protection token
         */
        return new Passport(
            new UserBadge($identifier),
            new PasswordCredentials($password),
            [$departmentBadge]
        );
    }

    /**
     * Shortcut to create a PostAuthenticationToken for you, if you don't really
     * care about which authenticated token you're using.
     */
    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        return new PostAuthenticationToken($passport->getUser(), $firewallName, $passport->getUser()->getRoles());
    }

    /**
     * Returns true if the credentials are valid.
     */
    public function checkCredentials(mixed $credentials, UserInterface $user): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $credentials->getPassword());
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword(mixed $p_credentials): ?string
    {
        return $p_credentials->getPassword();
    }

    /**
     * On event - onAuthenticationSuccess
     * Override to change what happens after successful authentication.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /**
         * Update last connection for this User
         *
         * @var User $user
         */
        $user = $token->getUser();
        $user->setLastLogin(new \DateTime('now'));

        $this->em->persist($token->getUser());
        $this->em->flush();

        /** Save information into request to store it later */
        $request->attributes->set(SecurityRequestAttributes::LAST_USERNAME, $user->getEmail());

        /** Store username used in session to get it quickly  */
        $session = $request->getSession();
        $session->set(SecurityRequestAttributes::LAST_USERNAME, $user->getEmail());

        if ($this->parameterBag->get('lucca_core.admin_domain_name') === $request->headers->get('host')) {
            // Redirect to the admin route after login
            return new RedirectResponse($this->urlGenerator->generate($this->adminRouteAfterLogin));
        }

        return new RedirectResponse($this->urlGenerator->generate($this->routeAfterLogin));
    }

    /**
     * On event - onAuthenticationFailure
     * Override to change what happens after a bad username/password is submitted.
     * Create a LoginAttempt entity and set it
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        /** In case we have a TooManyLoginAttemptsAuthenticationException we need to compare it to our login attempt to check if we really need to block or not */
        if ($exception instanceof TooManyLoginAttemptsAuthenticationException) {

            /** Get the node parameter and call specific parameter on by one */
            $parameterProtection = $this->parameterBag->get('lucca_security.protection');
            $now = new \DateTime('now');
            $periodScanLimit = $parameterProtection['period_max_in_min'];
            $loginsAttempt = $this->em->getRepository(LoginAttempt::class)->findAllByIpAddressAndLoginAttemptDate(
                $request->getClientIp(), $now->modify('-' . $periodScanLimit . ' minutes')
            );

            /** @var int $nbrTries */
            $nbrTries = $parameterProtection['max_login_attempts'];

            if ($loginsAttempt === [] || count($loginsAttempt) < $nbrTries) {
                $this->rateLimiter->reset($request);
                return new RedirectResponse($this->getLoginUrl($request));
            }
        }

        /** Save session messages to display them after redirection */
        $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);

        /** Register a new LoginAttempt */
        $this->loginAttemptManager->createLoginAttempt($request);

        return new RedirectResponse($this->getLoginUrl($request));
    }

    /**
     * Return the URL to the login page.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate($this->routeLogin);
    }

    public function supportsRememberMe(): void
    {
        // TODO: Implement supportsRememberMe() method.
    }
}
