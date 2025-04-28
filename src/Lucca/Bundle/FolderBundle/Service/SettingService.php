<?php

namespace Lucca\Bundle\FolderBundle\Service;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\MediaBundle\Entity\{Category, Folder};
use Lucca\Bundle\SettingBundle\Generator\{SettingGenerator, DataGenerator};

readonly class SettingService
{
    public function __construct(
        private DataGenerator          $dataGenerator,
        private EntityManagerInterface $em,
        private SettingGenerator       $settingGenerator,
    )
    {
    }

    public function createForDepartment(Department $department): void
    {
        $settings = $this->dataGenerator->settings;

        foreach ($settings as $setting) {
            $this->settingGenerator->insertOrUpdateSetting($setting['type'], $setting['category'], $setting['accessType'], $setting['position'],
                $setting['name'], $setting['value'], $setting['comment'], $setting['valuesAvailable'], $department,
            );
        }

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
}
