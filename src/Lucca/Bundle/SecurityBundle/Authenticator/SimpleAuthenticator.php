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
    Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Session\SessionInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Http\SecurityRequestAttributes,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Csrf\CsrfTokenManagerInterface,
    Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge,
    Symfony\Component\Security\Http\Authenticator\Passport\Passport,
    Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator,
    Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials,
    Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken,
    Symfony\Component\Security\Http\Util\TargetPathTrait,
    Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


use Lucca\Bundle\SecurityBundle\Manager\LoginAttemptManager;
use Lucca\Bundle\SecurityBundle\Exception\LoginWithoutSessionException;
use Lucca\Bundle\UserBundle\Entity\User;

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
        private readonly EntityManagerInterface    $em,
        private readonly UrlGeneratorInterface     $urlGenerator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly UserPasswordHasher        $passwordHasher,
        private readonly LoginAttemptManager       $loginAttemptManager,

        private TokenStorageInterface $tokenStorage, private RequestStack $requestStack,

        /** Define constant to name route called after a login */
        #[Autowire(param: 'lucca_security.default_url_after_login')]
        private readonly string                    $routeAfterLogin,
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

        /** Redirect to the last page visited before login */
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
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
