<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\{FormEvent, FormEvents};
use Symfony\Component\HttpFoundation\File\{File, UploadedFile};

use Lucca\Bundle\MediaBundle\Manager\FileManager;
use Lucca\Bundle\MediaBundle\Entity\Media;

readonly class MediaFormListener implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private FileManager   $fileManager,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    /**
     * On form load the file if we are on an edit
     */
    public function onPreSetData(FormEvent $event): ?File
    {
        $form = $event->getForm();
        $filesystem = new Filesystem();

        /** If the Media exists find it and find its path.
         * If it does not then return null.
         */
        if (!($event->getData() !== null && $event->getData()->getId())){
            return null;
        }

        $media = $this->em->getRepository(Media::class)->find($event->getData()->getId());
        $filePath = $this->fileManager->getMediaPath($media);

        /** If the the file or the media does not exist return null */
        if (!$media || !$filesystem->exists($filePath)) {
            return null;
        }

        /** If the Media exists Create a File instance with its path and set the file field with it. */
        $file = new File($this->fileManager->getMediaPath($media));
        $form->get('file')->setData($file);

        return $file;
    }

    /**
     * On submit check if the file changed and delete the old one. Then save it.
     * If the file gas not changed do nothing
     */
    public function onSubmit(FormEvent $event): ?FormEvent
    {
        $media = $event->getData();
        $form = $event->getForm();
        /** If the Media does not exist do nothing. The form is probably a new form */
        if ($media === null) {
            return null;
        }

        /** If the route method is delete, remove the file when the media is deleted trough a form. */
        if ($form->getConfig()->getMethod() === "DELETE") {
            $this->fileManager->removeFile($media);
        } elseif ($form['file']->getData() !== null) /* If a new file is submitted */ {
            /** If the route method is post and the file is a new one. Remove the previous one. */
            if ($media->getId()) {
                $this->fileManager->removeFile($media);
            }

            /**
             * Get File in tmp Folder
             */
            /** @var UploadedFile $fileToUpload */
            $file = $form['file']->getData();

            $filesystem = new Filesystem();
            if ($filesystem->exists($this->fileManager->getTmpPath() . '/' . $file->getClientOriginalName())) {
                // Create new uploadedFile with file params
                $fileToUpload = new UploadedFile($this->fileManager->getTmpPath() . '/' . $file->getClientOriginalName(),
                $file->getClientOriginalName(),
                $file->getClientMimeType(), $file->getSize() , $file->getError());

                /** With any post method upload the file */
                $this->fileManager->uploadFile($fileToUpload, $media);
            }
        }

        if ($media->isEmpty()) {
            /** If Media is empty (no name generated / path folder or other data used - delete it */
            $event->setData(null);
        }

        return $event;
    }
}
