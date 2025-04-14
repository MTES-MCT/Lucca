<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;

use Lucca\Bundle\DepartmentBundle\Entity\Department;

readonly class DoctrineFilterListener
{
    public function __construct(
        private RequestStack           $requestStack,
        private EntityManagerInterface $em,
    )
    {
    }

    /**
     * Call on every kernel request
     */
    #[AsEventListener(event: 'kernel.request')]
    public function onKernelRequest(RequestEvent $event): void
    {
        // Retrieve the subdomain from the HTTP request
        $currentRequest = $this->requestStack?->getCurrentRequest();
        $departmentCode = null;
        if ($currentRequest) {
            $hostParts = explode('.', $currentRequest->getHost());
            if (count($hostParts) > 2) {
                $departmentCode = $hostParts[0];
            }
        }

        if ($departmentCode == null) {
            // We make sure we are working on the main request.
            if (!$event->isMainRequest()) {
                return;
            }

            $department = $this->em->getRepository(Department::class)->findOneBy(['code' => $departmentCode]);

            if ($department) {
                $filter = $this->em->getFilters()->enable('department_filter');
                $filter->setParameter('department', $department->getId());
            }
        }
    }
}
