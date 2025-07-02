<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\EventListener;

use Lucca\Bundle\DepartmentBundle\Exception\DepartmentNotFoundException;
use Lucca\Bundle\DepartmentBundle\Exception\UnauthorizedDepartmentAccessException;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;

readonly class ExceptionListener
{
    public function __construct(
        private RouterInterface $router,
        private UserDepartmentResolver $userDepartmentResolver
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();


        if ($exception instanceof UnauthorizedDepartmentAccessException) {
            $departmentCode = $this->userDepartmentResolver->getCode(true);
            if ($departmentCode) {
                //redirect to the current route with the department code

                $currentRoute = $event->getRequest()->attributes->get('_route');

                $currentRoutParams = $event->getRequest()->attributes->get('_route_params') ?? [];

                $currentRoutParams['dep_code'] = $departmentCode;

                $url = $this->router->generate($currentRoute, $currentRoutParams);
                $event->setResponse(new RedirectResponse($url));
            } else {
                //if no department code, redirect to home and unlogged user
                $url = $this->router->generate('lucca_core_home');
                $event->setResponse(new RedirectResponse($url));
            }
        }

        if ($exception instanceof DepartmentNotFoundException) {
            $url = $this->router->generate('lucca_core_home');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}