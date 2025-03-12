<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
    * Called when entity is updated
 */
class EntityUpdateListener
{
    /**
     * Set a new update date
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'getUpdatedAt')) {
            $entity->setUpdatedAt(new \DateTime('now'));
        }
    }

    public function getName(): string
    {
        return 'lucca.listener.entity_update';
    }
}
