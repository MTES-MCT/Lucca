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
use Symfony\Component\HttpKernel\Event\RequestEvent;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
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
        $departmentCode = $this->userDepartmentResolver->getDepartmentCode();
        if ($departmentCode !== null) {
            // We make sure we are working on the main request.
            if (!$event->isMainRequest()) {
                return;
            }

            $department = $this->em->getRepository(Department::class)->findOneBy(['code' => $departmentCode]);

            if ($department) {
                $filter = $this->em->getFilters()->enable('department_filter');
                $filter->setParameter('department', $department->getId());

                // Injecter manuellement le RequestStack dans le filtre
                $filter->setDepartmentId($department->getId());
            }
        }
    }
}
