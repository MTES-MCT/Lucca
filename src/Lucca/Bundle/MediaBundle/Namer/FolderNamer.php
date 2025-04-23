<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Namer;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use Lucca\Bundle\CoreBundle\Utils\Canonalizer;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\MediaBundle\Entity\{Category, Media, Folder};

readonly class FolderNamer implements FolderNamerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Canonalizer            $canonalizer,
        private UserDepartmentResolver $userDepartmentResolver,

        #[Autowire(param: 'lucca_media.upload_directory')]
        private string                 $upload_dir,
    )
    {
    }

    /**
     * Search and attributed Folder to a Media
     */
    public function searchFolder(Media $media, $object = null): Folder
    {
        $department = $this->userDepartmentResolver->getDepartment();

        /**
         * Step 1 - Build folder path
         * Sort each file by Category / Extension
         */
        $extensionPath = $department->getCode() . '/' . $this->canonalizer->slugify($media->getCategory()->getName());
        $extensionPath .= '/' . pathinfo($media->getNameOriginal(), PATHINFO_EXTENSION);

        /**
         * Step 2 - Search Folder
         */
        $folder = $this->em->getRepository(Folder::class)->findByPath($extensionPath);

        /** If Folder Entity don't exist */
        if (!$folder) {
            $folder = $this->createNewFolder($media->getCategory(), $extensionPath);
        }

        $this->manageFilesystem($folder);

        /**
         * Step 3 - Associated Media and Folder
         */
        $media->setFolder($folder);

        return $folder;
    }

    /**
     * Create a new Folder entity by a specific Category
     * Check if Filesystem exist and create it
     */
    private function createNewFolder(Category $category, $extensionPath): Folder|Exception
    {
        /** Get Storager associated to the Media */
        $storager = $category->getStorager();

        /**
         * Step - Create Entity
         */
        $folder = new Folder();
        $folder->setName($extensionPath);
        $folder->setNameCanonical($this->canonalizer->slugify($folder->getName()));
        $folder->setPath($extensionPath);

        $storager->addFolder($folder);

        try {
            $this->em->persist($folder);
            $this->em->persist($storager);
        } catch (ORMException $exception) {
            return new Exception('Folder or Storager cannot be persisted - ' . $exception->getMessage());
        }

        return $folder;
    }

    /**
     * Check if Filesystem exist and create it
     */
    private function manageFilesystem(Folder $folder): Folder|Exception
    {
        /**
         * Step 1 - Create folder in filesystem
         */
        $filesystem = new Filesystem();

        /** TODO use just one function to create a complete path with param */
        $path = $this->upload_dir . '/' . $folder->getPath();

        if (!$filesystem->exists($path)) {
            /** Try to make directory in media upload directory */
            try {
                $filesystem->mkdir($path);
            } catch (IOExceptionInterface $exception) {
                return new Exception('Folder cannot be created in filesystem - ' . $exception->getMessage());
            }
        }

        return $folder;
    }
}
