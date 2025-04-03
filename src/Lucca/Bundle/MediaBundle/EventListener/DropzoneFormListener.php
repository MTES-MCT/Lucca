<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\EventListener;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\{FormEvent, FormEvents};
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Lucca\Bundle\MediaBundle\Entity\{Media, MediaListAsyncInterface};
use Lucca\Bundle\MediaBundle\Manager\FileManager;

readonly class DropzoneFormListener implements EventSubscriberInterface
{
    public function __construct(
        private FileManager   $fileManager,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            'onPostSetData' => FormEvents::POST_SET_DATA,
            'onPreSubmit' => FormEvents::PRE_SUBMIT,
        );
    }

    /**
     * On form load the file if we are on an edit
     */
    public function onPostSetData(FormEvent $event): ?array
    {
        $form = $event->getForm()->getParent();

        $filesystem = new Filesystem();

        // Test if the form class implement MediaListAsyncInterface to get function as addMedia, removeMedia, getMedias ...
        if (in_array(MediaListAsyncInterface::class, class_implements($form->getData()))) {

            // Medias is medias of the form
            $medias = $form->getData()->getAsyncMedias();
            $files = [];

            /** If the Media exists find it and find its path.
             * If it does not then return null.
             */
            if (!($medias !== null && count($medias) > 0 )) {
                return null;
            }

            // For each Medias, test if the file exist
            foreach ($medias as $media) {
                if ($media instanceof Media) {
                    $filePath = $this->fileManager->getMediaPath($media);

                    /** If the the file or the media does not exist continue */
                    if (!$filesystem->exists($filePath)) {
                        continue;
                    }

                    $files[] = $media;
                }
            }
            $event->getForm()->get('file')->setData($files);

            return $files;
        }

        return null;
    }

    /**
     * On submit get all files sent on tmp folder and create medias.
     *
     * @throws ORMException
     */
    public function onPreSubmit(FormEvent $event): ?FormEvent
    {
        /**
        * Get File in tmp Folder
        */
        /** @var string $filesString */
        $filesString = $event->getData()['file'][0];

        // Create an array with this filesString
        /** @var array $files */
        $files = explode(';;;', $filesString);
        array_pop($files);

        // Get form parent of the dropzone
        $parentForm = $event->getForm()->getParent();

        // Test if the form class implement MediaListAsyncInterface to get function as addMedia, removeMedia, getMedias ...
        if (in_array(MediaListAsyncInterface::class, class_implements($parentForm->getData()))) {

            /** If the dropzone does not exist do nothing. The form is probably a new form */
            if ($files === null) {
                return null;
            }

            $filesystem = new Filesystem();

            foreach ($files as $file) {
                /**
                 * $file = 'fileName;fileType'
                 * $fileObject[0] = fileName
                 * $fileObject[1] = fileType
                 */
                $fileObject = explode(';;', $file);
                if ($filesystem->exists($this->fileManager->getTmpPath() . '/' . $fileObject[0]) && array_key_exists(1, $fileObject)) {
                    // Create new uploadedFile with file params
                    $fileToUpload = new UploadedFile($this->fileManager->getTmpPath() . '/' . $fileObject[0],
                    $fileObject[0],
                    $fileObject[1]);

                    /** With any post method upload the file */
                    $media = $this->fileManager->uploadFile($fileToUpload, new Media());
                    $parentForm->getData()->addAsyncMedia($media);
                }
            }
        }

        return $event;
    }
}
