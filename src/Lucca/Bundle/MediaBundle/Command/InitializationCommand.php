<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Command;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

use Lucca\Bundle\MediaBundle\Entity\{Category, Extension, Storager};
use Lucca\Bundle\MediaBundle\Namer\{FolderNamerInterface, MediaNamerInterface};

class InitializationCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('lucca:init:media')
            ->setDescription('Initialize MediaBundle with default params.')
            ->setHelp('Initialize MediaBundle with default params - Storager / Extension wildcard / ...');
    }

    /**
     * Execute command
     * Write log and start the import
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        // Start command
        $now = new \DateTime('now');
        $output->writeln([
            '',
            'Initialize default configuration in Lucca',
            'Start: ' . $now->format('d-m-Y H:i:s'),
            '-----------------------------------------------',
        ]);

        // Command logic stored here
        $this->initMediaBundle($input, $output);

        // Showing when the script is over
        $now = new \DateTime('now');
        $output->writeln([
            '',
            '-----------------------------------------------',
            'End: ' . $now->format('d-m-Y H:i:s'),
        ]);

        $finishTime = microtime(true);
        $elapsedTime = $finishTime - $startTime;
        $output->writeln([
            '',
            sprintf('<comment>[INFO] Import stored in database: Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
            '-----------------------------------------------',
        ]);

        return 1;
    }

    /**
     * Import from file and create objects
     */
    protected function initMediaBundle(InputInterface $input, OutputInterface $output): void
    {
        $em = $this->em;

        // Turning off doctrine default logs queries for saving memory
        $em->getConfiguration()->setSQLLogger(null);

        /** Search default Category - if not create it */
        $category = $em->getRepository(Category::class)->findDefaultCategory();

        if (!$category) {
            $category = new Category();
            $category->setName(Category::DEFAULT_NAME);
            $category->setDescription('Default category for all medias');

            $output->writeln('Create default Category and save it');
        } else {
            $output->writeln('Default Category existing !');
        }


        /** Search default Extension - if not create it and associated to Category */
        if ($category->hasExtension(Storager::DEFAULT_NAME) === false) {
            $extension = new Extension();
            $extension->setName(Extension::DEFAULT_NAME);
            $extension->setValue('*');
            $extension->setDescription('Default wildcard to store all medias by default');

            $category->addExtension($extension);
//            $em->persist($extension);
            $output->writeln('Create default Extension and save it');
        } else {
            $output->writeln('Wildcard Extension existing !');
        }


        /** Search default Storager - if not create it */
        $storager = $em->getRepository(Storager::class)->findByName(Storager::DEFAULT_NAME);

        if (!$storager) {
            $storager = new Storager();

            $storager->setName(Storager::DEFAULT_NAME);
            $storager->setServiceFolderNaming(FolderNamerInterface::NAMER_FOLDER_BY_DATE);
            $storager->setServiceMediaNaming(MediaNamerInterface::NAMER_MEDIA);
            $storager->setDescription('Default Storager for defaultCategory');

            $category->setStorager($storager);
            $em->persist($storager);
            $output->writeln('Create default Storager and save it');
        } else {
            $output->writeln('Storager existing !');
        }

        $em->persist($category);

        /** flush last items, detach all for doctrine and finish progress */
        $em->flush();
        $em->clear();
    }
}
