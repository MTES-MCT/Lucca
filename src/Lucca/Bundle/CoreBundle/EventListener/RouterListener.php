<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\EventListener;

use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RequestContext;

readonly class RouterListener implements EventSubscriberInterface
{
    public function __construct(
        private RequestContext         $requestContext,
        private UserDepartmentResolver $userDepartmentResolver
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->requestContext->hasParameter('dep_code')) {
            $depCode = $this->userDepartmentResolver->getCode();
            if ($depCode) {
                $this->requestContext->setParameter('dep_code', $depCode);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }
}