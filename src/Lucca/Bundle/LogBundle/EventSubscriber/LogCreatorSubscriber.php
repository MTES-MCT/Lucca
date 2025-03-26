<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\LogBundle\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\{EntityManagerInterface, UnitOfWork, OptimisticLockException};
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface,
    Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Lucca\Bundle\LogBundle\Entity\Log,
    Lucca\Bundle\LogBundle\Event\LogCreatorEvent,
    Lucca\Bundle\LogBundle\Entity\LoggableInterface,
    Lucca\Bundle\UserBundle\Entity\User;

/**
 * Called when LogEvent is triggered
 * TODO Collection entities are managed in a different collection of UnitOfWork
 * TODO maybe we can log anonymous actions on database
 */
readonly class LogCreatorSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private ManagerRegistry        $managerRegistry,
        private TokenStorageInterface  $tokenStorage,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogCreatorEvent::NAME => 'createLogEntities'
        ];
    }

    /**
     * Create Log Entities for each entity passed from the LogCreatorEvent
     */
    public function createLogEntities(LogCreatorEvent $logEvent): bool
    {
        /** @var TokenInterface userToken */
        $userToken = $this->tokenStorage->getToken();

        /**
         * If no user has been authenticated - don't log anything
         */
        if ($userToken === null or !($userToken->getUser() instanceof User)) {
            return false;
        }

        /** @var User $user */
        $user = $userToken->getUser();

        /** UnitOfWork required to find any entities */
        $uow = $this->em->getUnitOfWork();

        /** EntityManager required to send request of repository */
        $em = $this->em;

        $flagPersistedInsertion = false;
        $flagPersistedUpdate = false;
        $flagPersistedDelete = false;

        /** Log any insertion data */
        if (isset($logEvent->getListEntities()['INSERTIONS']) && sizeof($logEvent->getListEntities()['INSERTIONS']) > 0) {
            $flagPersistedInsertion = $this->logEntityChangeSets($em, $uow, Log::STATUS_INSERT, $user, $logEvent->getListEntities()['INSERTIONS']);
        }

        /** Log any update data */
        if (isset($logEvent->getListEntities()['UPDATES']) && sizeof($logEvent->getListEntities()['UPDATES']) > 0) {
            $flagPersistedUpdate = $this->logEntityChangeSets($em, $uow, Log::STATUS_UPDATE, $user, $logEvent->getListEntities()['UPDATES']);
        }

        /** Log any deletion data */
        if (isset($logEvent->getListEntities()['DELETIONS']) && sizeof($logEvent->getListEntities()['DELETIONS']) > 0) {
            $flagPersistedDelete = $this->logEntityChangeSets($em, $uow, Log::STATUS_REMOVE, $user, $logEvent->getListEntities()['DELETIONS']);
        }

        if ($flagPersistedInsertion || $flagPersistedUpdate || $flagPersistedDelete) {
            try {
                $em->flush();
            } catch (OptimisticLockException | ORMException $e) {
                echo 'Log subscriber can not flush all changes - ' . $e->getMessage();
            }
        }

        /** IMPORTANT -- Trigger just one time this event - Avoid cascade log */
        $logEvent->stopPropagation();

        return true;
    }

    /**
     * Log change set on any entity flushed
     */
    private function logEntityChangeSets(EntityManagerInterface $em, UnitOfWork $uow, string $status, User $user, array $listEntities): bool
    {
        $flagPersisted = false;

        /**
         * $p_listEntities is an array constructed on Nw\LogBundle\EventSubscriber\DoctrineSubscriber
         *
         * Data structure is like :
         * - 'id' of the object
         * - 'entity' instance of this object
         */

        foreach ($listEntities as $element) {
            /** @var mixed $entity - Instance of an object */
            $entity = $element['entity'];

            /**
             * Define id
             * if its a new entity - id is set by doctrine
             * if its an update or a remove - id is store in array
             */
            $entityId = null;
            if ($element['id'] === null) {
                $entityId = $entity->getId();
            } else {
                /** Id of this entity */
                $entityId = $element['id'];
            }

            /** Check if Entity implements LoggableInterface and can be log */
            if (in_array(LoggableInterface::class, class_implements($entity), true)) {

                $classMetaData = $this->managerRegistry->getManager()->getClassMetadata(get_class($entity));

                /** Create and init a Log entity */
                $log = new Log();
                $log->setUser($user);
                $log->setClassname($classMetaData->getName());
                $log->setStatus($status);
                $log->setObjectId($entityId);

                /** Generate shortMessage and message depends on status */
                switch ($status) {
                    case Log::STATUS_INSERT:
                        $log->setShortMessage('L\'utilisateur ' . $user->getUsername() . ' a crée un nouveau ' . $entity->getLogName() . ' - ' . $entityId . '.');
                        $log->setMessage($this->logChangeSet($uow, $entity));
                        break;

                    case Log::STATUS_REMOVE:
                        $log->setShortMessage('L\'utilisateur ' . $user->getUsername() . ' a supprimé le ' . $entity->getLogName() . ' - ' . $entityId . '.');
                        break;

                    case Log::STATUS_UPDATE:
                        $log->setShortMessage('L\'utilisateur ' . $user->getUsername() . ' a modifié le ' . $entity->getLogName() . ' - ' . $entityId . '.');
                        $log->setMessage($this->logChangeSet($uow, $entity));

                        /** Last Connection at update */
                        if ($entity instanceof User and array_key_exists('lastConnectionAt', $uow->getEntityChangeSet($entity)))
                            $log->setStatus(Log::STATUS_CONNECTION);
                        break;

                    default:
                        // nothing
                        break;
                }

                try {
                    $em->persist($log);
                } catch (ORMException $ORMException) {
                    echo 'Log entity cannot be persisted - ' . $ORMException->getMessage();
                }

                /** An object has been persisted so flag this on true */
                $flagPersisted = true;
            }
        }

        return $flagPersisted;
    }

    /**
     * Check if any changeSet has been register and return it
     */
    private function logChangeSet(UnitOfWork $uow, mixed $element): bool|string|null
    {
        $changeSet = $uow->getEntityChangeSet($element);

        /** For each change check if its an Entity who implements LoggableInterface or other */
        foreach ($changeSet as $key => $change) {
            $line = [];

            /** Init state */
            if ($change[0] instanceof LoggableInterface) {
                $line[0] = $change[0]->getId();
            } else {
                $line[0] = $change[0];
            }

            /** New state */
            if ($change[1] instanceof LoggableInterface) {
                $line[1] = $change[1]->getId();
            } else {
                $line[1] = $change[1];
            }

            $changeSet[$key] = $line;
        }

        if (count($changeSet) > 0) {
            return json_encode($changeSet);
        }

        return null;
    }
}
