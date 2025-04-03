<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccessDeniedHandler
{
    /**
     * Check every response if there is 403 code if found it  throw new AccessDeniedHttpException()
     * in order to display error message when user is not in DB (Cas auth on invalid user)
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($event->getResponse()->getStatusCode() === 403) {
            /** If content contain HTML it's mean it's already the error page so don't throw exception again */
            if (strpos($event->getResponse()->getContent(), "html") == null) {
                throw new AccessDeniedHttpException();
            }
        }
    }
}
