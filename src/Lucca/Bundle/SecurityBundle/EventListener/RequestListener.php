<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\EventListener;

use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Event\RequestEvent,
    Symfony\Component\Routing\RouterInterface,
    Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface,
    Doctrine\ORM\EntityManagerInterface;

use Lucca\Bundle\SecurityBundle\Repository\LoginAttemptRepository;
use Lucca\Bundle\SecurityBundle\Entity\LoginAttempt;

class RequestListener
{
    private array $routes;

    /**
     * RequestListener constructor.
     */
    function __construct(
        private readonly ParameterBagInterface  $parameterBag,
        private readonly EntityManagerInterface $em,
        private readonly RouterInterface        $router,
        private readonly UserDepartmentResolver $userDepartmentResolver,
    )
    {
        $this->routes = array('lucca_user_security_login');
    }

    /**
     * Call on every kernel request
     * Check LoginAttempts made with Ip on request
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() or !in_array($event->getRequest()->get('_route'), $this->routes, true)) {
            return;
        }

        /** Get the node parameter and call specific parameter on by one */
        $parameterProtection = $this->parameterBag->get('lucca_security.protection');

        $now = new \DateTime('now');
        $periodScanLimit = $parameterProtection['period_max_in_min'];

        /** @var LoginAttemptRepository $repository */
        $repository = $this->em->getRepository(LoginAttempt::class);

        $loginsAttempt = $repository->findAllByIpAddressAndLoginAttemptDate(
            $event->getRequest()->getClientIp(), $now->modify('-' . $periodScanLimit . ' minutes')
        );


        if ($loginsAttempt === []) {
            return;
        }

        /** @var int $nbrTries */
        $nbrTries = $parameterProtection['max_login_attempts'];

        if (count($loginsAttempt) > $nbrTries && $event->getResponse() === null) {
            $event->setResponse(new RedirectResponse($this->router->generate('lucca_security_ip_banned', [
                'dep_code' => $this->userDepartmentResolver->getCode()
            ])));
        }
    }
}
