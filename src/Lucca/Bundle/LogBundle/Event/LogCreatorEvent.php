<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\LogBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class LogCreatorEvent extends Event
{
    /** Name of this event */
    const NAME = 'lucca.log.event.creator';

    protected array $listEntities;

    /**
     * LogEvent constructor
     */
    public function __construct(array $listEntities)
    {
        $this->listEntities = $listEntities;
    }

    /**
     * Get listEntities
     */
    public function getListEntities(): array
    {
        return $this->listEntities;
    }
}
