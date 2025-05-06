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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\MediaBundle\Entity\MediaListAsyncInterface;
use Lucca\Bundle\MediaBundle\Manager\FileManager;

readonly class DropzoneFormListener implements EventSubscriberInterface
{
    public function __construct(
        private FileManager $fileManager
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SET_DATA => 'onPostSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    /**
     * On form load, load files if we are editing
     */
    public function onPostSetData(FormEvent $event): void
    {
        $form = $event->getForm()->getParent();

        if (!$form) {
            return;
        }

        $data = $form->getData();

        if (!$data || !in_array(MediaListAsyncInterface::class, class_implements($data))) {
            return;
        }

        $filesystem = new Filesystem();
        $medias = $data->getAsyncMedias();
        $files = [];

        if ($medias && count($medias) > 0) {
            foreach ($medias as $media) {
                if ($media instanceof Media) {
                    $filePath = $this->fileManager->getMediaPath($media);
                    if ($filesystem->exists($filePath)) {
                        $files[] = $media;
                    }
                }
            }
        }

        if (!empty($files)) {
            $event->getForm()->get('file')->setData($files);
        }
    }

    /**
     * On submit, create media from uploaded temp files
     *
     * @throws ORMException
     */
    public function onPreSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        if (!isset($data['file'][0]) || empty($data['file'][0])) {
            return;
        }

        $filesString = $data['file'][0];
        $files = explode(';;;', $filesString);
        array_pop($files); // Remove last empty element if any

        $parentForm = $event->getForm()->getParent();

        if (!$parentForm) {
            return;
        }

        $formData = $parentForm->getData();

        if (!$formData || !in_array(MediaListAsyncInterface::class, class_implements($formData))) {
            return;
        }

        $filesystem = new Filesystem();

        foreach ($files as $file) {
            $fileObject = explode(';;', $file);

            if (
                isset($fileObject[0], $fileObject[1]) &&
                $filesystem->exists($this->fileManager->getTmpPath() . '/' . $fileObject[0])
            ) {
                $fileToUpload = new UploadedFile(
                    $this->fileManager->getTmpPath() . '/' . $fileObject[0],
                    $fileObject[0],
                    $fileObject[1]
                );

                $media = $this->fileManager->uploadFile($fileToUpload, new Media());
                $formData->addAsyncMedia($media);
            }
        }
    }
}
