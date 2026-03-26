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
use Lucca\Bundle\DepartmentBundle\Doctrine\Filter\DepartmentFilter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;

readonly class DoctrineFilterListener
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserDepartmentResolver $userDepartmentResolver,
    )
    {
    }

    /**
     * Call on every kernel request
     */
    #[AsEventListener(event: 'kernel.request')]
    public function onKernelRequest(RequestEvent $event): void
    {
        $department = $this->userDepartmentResolver->getDepartment();
        if ($department !== null) {
            // We make sure we are working on the main request.
            if (!$event->isMainRequest()) {
                return;
            }

            if ($department) {
                /** @var DepartmentFilter $filter */
                $filter = $this->em->getFilters()->enable('department_filter');
                $filter->setParameter('department', $department->getId());

                // Inject department ID into the filter
                $filter->setDepartmentId($department->getId());
            }
        }
    }
}
