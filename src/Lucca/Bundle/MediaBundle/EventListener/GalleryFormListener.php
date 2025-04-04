<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\EventListener;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\{FormEvent, FormEvents};
use Symfony\Component\HttpFoundation\File\File;

use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\MediaBundle\Manager\FileManager;

readonly class GalleryFormListener implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private FileManager            $fileManager,
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
     * Get all files already saved in the gallery
     */
    public function onPreSetData(FormEvent $event): null
    {
        $form = $event->getForm();
        $filesystem = new Filesystem();

        /**
         * If the Gallery exists find it and find its path.
         * If it does not then return null.
         */
        if ($event->getData() !== null && $event->getData()->getId()) {
            $files = [];

            foreach ($event->getData()->getMedias() as $media) {

                $filePath = $this->fileManager->getMediaPath($media);

                if ($media && $filesystem->exists($filePath)) {
                    array_push($files, new File($filePath));
                }
            }

            $form->get('file')->setData($files);
        }

        return null;
    }

    /**
     * On submit check if a file is missing and delete it.
     * Then if a file is new, it saves it.
     *
     * @throws Exception
     */
    public function onSubmit(FormEvent $event): null|Exception|FormEvent
    {
        $gallery = $event->getData();
        $form = $event->getForm();

        /** If the Gallery does not exist do nothing. The form is probably a new form */
        if ($gallery === null) {
            return null;
        }

        /** If the route method is delete, remove the file when the gallery is deleted. */
        if ($event->getForm()->getConfig()->getMethod() === "DELETE") {
            $this->fileManager->removeFile($gallery);
        }

        /** If the route method is post and the file is a new one. Remove the previous one. */
        elseif ($form['file']->getData()) {

            foreach ($form['file']->getData() as $file) {
                $media = new Media();
                /** With any post method upload the file */
                $this->fileManager->uploadFile($file, $media);
                $gallery->addMedia($media);
            }

            try {
                $this->em->persist($gallery);
            } catch (ORMException $exception) {
                return new Exception('Gallery cannot be persisted - ' . $exception->getMessage());
            }
        }

        return $event;
    }
}
