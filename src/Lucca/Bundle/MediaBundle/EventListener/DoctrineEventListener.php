<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\EventListener;

use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Filesystem\Exception\IOException;

use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\MediaBundle\Manager\FileManager;

readonly class DoctrineEventListener
{
    public function __construct(
        private FileManager $fileManager,
    )
    {
    }

    /**
     * Pre remove event listener.
     */
    #[AsEventListener(event: Events::preRemove)]
    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        /** @var Media $entity */
        if ($entity instanceof Media) {
            try {
                /** Remove file system and Media entity */
                $this->fileManager->removeFile($entity);
            } catch (IOException $exception) {
                echo 'Error when remove Media and his filesystem - ' . $exception->getMessage();
            }
        }
    }
}
