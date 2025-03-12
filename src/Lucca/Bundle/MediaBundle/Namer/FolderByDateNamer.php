<?php

namespace Lucca\Bundle\MediaBundle\Namer;

use DateTime;
use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use Lucca\Bundle\CoreBundle\Utils\Canonalizer;
use Lucca\Bundle\MediaBundle\Entity\{Category, Media, Folder};

class FolderByDateNamer implements FolderNamerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Canonalizer $canonalizer,
        private Folder $currentFolder,
        private string $upload_dir,
    )
    {
    }

    /**
     * Search and attributed Folder to a Media
     */
    public function searchFolder(Media $media, ?DateTime $createdAt = null): Folder
    {
        /**
         * Step 1 - Build folder path
         * eg : year/week - 2019/52
         */
        if ($createdAt) {
            $date = $createdAt;
        } else {
            $date = new DateTime('now');
        }
        $pathDate = $date->format('Y') . '/' . $date->format('W');

        /**
         * Step 2 - Search Folder
         */

        /** Check if a folder has been created by this service */
        if ($this->currentFolder instanceof Folder && $this->currentFolder->getPath() === $pathDate)
            $folder = $this->currentFolder;
        else {
            /** Execute a request to search if this Folder has been created in database */
            $folder = $this->em->getRepository(Folder::class)->findByPath($pathDate);
        }

        /** If Folder Entity don't exist */
        if (!$folder) {
            $folder = $this->createNewFolder($media->getCategory(), $pathDate);
            $this->currentFolder = $folder;
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
    private function createNewFolder(Category $category, $entityPath): Folder
    {
        /** Get Storager associated to the Media */
        $storager = $category->getStorager();

        /**
         * Step - Create Entity
         */
        $folder = new Folder();
        $folder->setName($entityPath);
        $folder->setNameCanonical($this->canonalizer->slugify($folder->getName()));
        $folder->setPath($entityPath);

        $storager->addFolder($folder);

        try {
            $this->em->persist($folder);
            $this->em->persist($storager);
        } catch (ORMException $exception) {
            echo 'Folder or Storager cannot be persisted - ' . $exception->getMessage();
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

    public function getName(): string
    {
        return 'lucca.namer.folder_by_date';
    }
}
