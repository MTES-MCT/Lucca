<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\LogBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener,
    Doctrine\ORM\Event\OnFlushEventArgs,
    Doctrine\ORM\Events,
    Symfony\Component\EventDispatcher\EventDispatcherInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Lucca\Bundle\LogBundle\Event\LogCreatorEvent,
    Lucca\Bundle\UserBundle\Entity\User;

/**
 * Called when Doctrine event is called
 */
#[AsDoctrineListener(event: Events::onFlush, connection: "default")]
#[AsDoctrineListener(event: Events::postFlush, connection: "default")]
class DoctrineSubscriber
{
    public function __construct(
        private readonly EventDispatcherInterface   $eventDispatcher,
        private readonly TokenStorageInterface      $tokenStorage,
        private $entities = [],
    )
    {
    }

    /**
     * Function called with the event : OnFlush
     * Store all entities managed by UnitOfWork
     */
    public function onFlush(OnFlushEventArgs $args): bool
    {
        /** @var TokenInterface userToken */
        $userToken = $this->tokenStorage->getToken();

        if ($userToken === null or !($userToken->getUser() instanceof User)) {
            return false;
        }

        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        $this->entities = [];

        /**
         * Create a data structure to store 2 data for each object managed by the UOW :
         * - 'id' of the object
         * - 'entity' instance of this object
         *
         * Each entities is managed with this method
         */

        /** Store any insertions data */
        if (sizeof($uow->getScheduledEntityInsertions()) > 0) {
            /** Add an array to manage insertions entities */
            $this->entities['INSERTIONS'] = [];

            /** For each entity inserted by the uow and add this on array */
            foreach ($uow->getScheduledEntityInsertions() as $entityInsertion)
                $this->entities['INSERTIONS'][] = [
                    'id' => null,
                    'entity' => $entityInsertion
                ];
        }

        /** Store any updates data */
        if (sizeof($uow->getScheduledEntityUpdates()) > 0) {
            /** Add an array to manage updates entities */
            $this->entities['UPDATES'] = [];

            /** For each entity updates by the uow and add this on array */
            foreach ($uow->getScheduledEntityUpdates() as $entityUpdate)
                $this->entities['UPDATES'][] = [
                    'id' => $entityUpdate->getId(),
                    'entity' => $entityUpdate
                ];
        }

        /** Store any entities and these id deleted by the UnitOfWork */
        if (sizeof($uow->getScheduledEntityDeletions()) > 0) {
            /** Add an array to manage deletions entities */
            $this->entities['DELETIONS'] = [];

            /** For each entity deleted by the uow and add this on array */
            foreach ($uow->getScheduledEntityDeletions() as $entityDeletion)
                $this->entities['DELETIONS'][] = [
                    'id' => $entityDeletion->getId(),
                    'entity' => $entityDeletion
                ];
        }

        return true;
    }

    /**
     * Function called with the event : PostFlush
     * Send all entities stored to LogEvent
     */
    public function postFlush(): void
    {
        /** Trigger LogEvent only if entities is persisted / flushed */
        if (sizeof($this->entities) > 0) {
            $event = new LogCreatorEvent($this->entities);
            $this->eventDispatcher->dispatch($event, LogCreatorEvent::NAME);
        }
    }
}
