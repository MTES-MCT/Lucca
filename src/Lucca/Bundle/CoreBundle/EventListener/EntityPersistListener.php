<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

/**
 * Called when entity is created
 */
#[AsDoctrineListener(event: Events::prePersist)]
class EntityPersistListener
{
    /**
     * Set a new creation date
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'getCreatedAt')) {
            $entity->setCreatedAt(new \DateTimeImmutable('now'));
        }
    }
}
