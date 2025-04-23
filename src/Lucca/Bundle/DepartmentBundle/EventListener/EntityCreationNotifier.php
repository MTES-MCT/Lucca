<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;

#[AsDoctrineListener('prePersist')]
readonly class EntityCreationNotifier
{
    public function __construct(
        private UserDepartmentResolver $userDepartmentResolver,
    ) {
    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();
        $hasDepartment = property_exists($entity, 'department');
        $hasDepartments = property_exists($entity, 'departments');
        if (!$hasDepartment && !$hasDepartments) {
            return;
        }

        $department = $this->userDepartmentResolver->getDepartment();
        if ($hasDepartment && $department) {
            $entity->setDepartment($department);
        }

        if ($hasDepartments && $department) {
            $entity->addDepartment($department);
        }
    }
}
