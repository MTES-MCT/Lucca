<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Manager;

use DateTime;
use Exception;
use Doctrine\ORM\{EntityManagerInterface, EntityNotFoundException};
use Doctrine\ORM\Exception\ORMException;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Psr\Container\ContainerInterface;

use Lucca\Bundle\MediaBundle\Entity\{Category, Media, Folder, Storager};
use Lucca\Bundle\MediaBundle\Namer\{FolderByDateNamer,
    FolderNamer,
    FolderNamerInterface,
    MediaNamer,
    MediaNamerInterface};
use Lucca\Bundle\UserBundle\Entity\User;

readonly class FileManager implements ServiceSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface  $em,
        private ContainerInterface $locator,
        private TokenStorageInterface   $tokenStorage,

        #[Autowire(param: 'lucca_media.upload_directory')]
        private string                 $upload_dir,

        #[Autowire(param: 'lucca_media.upload_tmp_directory')]
        private string                 $upload_temp_dir,
    )
    {
    }

    public static function getSubscribedServices(): array
    {
        return [
            'lucca.namer.media' => MediaNamer::class,
            'lucca.namer.folder' => FolderNamer::class,
            'lucca.namer.folder_by_date' => FolderByDateNamer::class,
        ];
    }

    /**
     * Upload a file and create a Media Entity
     *
     * @throws ORMException
     */
    public function uploadFile(File $file, Media $media, ?DateTime $createdAt = null): Media|EntityNotFoundException
    {
        /** Get the file name */
        $fileName = $file->getClientOriginalName();

        /**
         * Step 1 - Find Category if Media has not Category directly associated
         */
//        if ($media->getCategory() === null) {
            /** Extract extension file */
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);

            /** Search the first Category who managed this extension */
            $category = $this->em->getRepository(Category::class)->findOneCategoryByExtension($extension);

            /** If no category has been found - Then take default Category */
            if (!$category) {
                $category = $this->em->getRepository(Category::class)->findDefaultCategory();
            }

            /** Assign Category entity to the Media */
            $media->setCategory($category);
//        }

        /**
         * Step 2 - Check if Category is configured and its Storager is configured
         */
        // If no Category or no Storager - Throw new exception
        if (!$media->getCategory() || !$media->getCategory()->getStorager()) {
            return new EntityNotFoundException("Category or Storager media cannot be found !");
        }

        /** @var $storager Storager - Get the Storager =) and next step */
        $storager = $media->getCategory()->getStorager();

        /**
         * Step 3 - Create Media Name
         */
        /** @var $serviceMediaNaming MediaNamerInterface */
        $serviceMediaNaming = $this->locator->get($storager->getServiceMediaNaming());

        /** Create names by a specific service */
        $media = $serviceMediaNaming->createMediaName($media, $fileName);

        /**
         * Step 4 - Define the folder
         */
        /** @var $serviceFolderNaming FolderNamerInterface */
        $serviceFolderNaming = $this->locator->get($storager->getServiceFolderNaming());

        $folder = $serviceFolderNaming->searchFolder($media, $createdAt);

        /**
         * Step 5 - Basic data of media
         */
        $media->setFilePath($folder->getPath() . "/" . $media->getNameCanonical());

        /** If we can't find a token that mean this function is called from the migration command
         * so set the owner to the nw user
         */
        if($this->tokenStorage->getToken()) {
            $media->setOwner($this->tokenStorage->getToken()->getUser());
        } else{
            $user = $this->em->getRepository(User::class)->findOneBy(array(
                'username' => 'lucca-nw-01'
            ));
            $media->setOwner($user);
        }
        $media->setMimeType($file->getClientMimeType());

        $filesystem = new Filesystem();
        /**
         * Step 6 - Rename file in specific directory
         */
        $filesystem->rename($file->getPathname(), $this->getFolderPath($folder) . "/" .$media->getNameCanonical());

        /** If the file exist in its new destination flush the media. */
        if ($filesystem->exists($this->getFolderPath($folder) . "/" . $media->getNameCanonical())) {
            $this->em->persist($media);
        }

        return $media;
    }
    /**
     * Upload a temp file
     *
     * @throws Exception
     */
    public function uploadTmpFile(File $file): bool
    {
        /** Get the file name */
        $fileName = $file->getClientOriginalName();

        /**
         * Step 1 - Get folder where tmp file are uploaded
         */
        $tmpFolder = $this->getTmpPath();

        /**
         * Step 2 - Move file in tmp directory
         */
        try {
            $file->move($tmpFolder, $fileName);
        } catch (Exception) {
            return false;
        }

        /**
         * Step 3 - Test if the upload worked
         */
        $filesystem = new Filesystem();

        /** If the file exist in its new destination return true else return false. */
        return $filesystem->exists($tmpFolder . '/' . $fileName);
    }

    /**
     * Delete a file
     */
    public function removeFile(Media $media): void
    {
        $filesystem = new Filesystem();

        /** Get the complete file path */
        $path = $this->getMediaPath($media);

        /**
         * Check if the complete file path exists.
         * If it does remove it.
         */
        if ($filesystem->exists($path)) {
            $filesystem->remove([$path]);
        }
    }

    /**
     * Create a complete path with upload_directory and file path
     */
    public function getMediaPath(Media $media): string
    {
        return $this->upload_dir . '/' . $media->getFilePath();
    }

    /**
     * Create a complete path with upload_directory and file path
     */
    public function getFolderPath(Folder $folder): string
    {
        return $this->upload_dir . '/' . $folder->getPath();
    }

    /**
     * Create a complete path with param %lucca_media.upload_tmp_dir%
     */
    public function getTmpPath(): string
    {
        return $this->upload_temp_dir;
    }
}
