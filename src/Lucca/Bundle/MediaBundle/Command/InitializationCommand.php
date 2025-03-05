<?php

namespace Lucca\Bundle\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Doctrine\Common\Persistence\ObjectManager;
use Lucca\Bundle\MediaBundle\Entity\Category;
use Lucca\Bundle\MediaBundle\Entity\Extension;
use Lucca\Bundle\MediaBundle\Entity\Storager;
use Lucca\Bundle\MediaBundle\Namer\FolderNamerInterface;
use Lucca\Bundle\MediaBundle\Namer\MediaNamerInterface;

/**
 * Class InitializationCommand
 *
 * @package Lucca\MediaBundle\Command
 * @author Terence <terence@numeric-wave.tech>
 */
class InitializationCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * Configure command parameters
     */
    protected function configure()
    {
        $this
            ->setName('lucca:init:media')
            ->setDescription('Initialize MediaBundle with default params.')
            ->setHelp('Initialize MediaBundle with default params - Storager / Extension wildcard / ...');
    }

    /**
     * Initialize var for process from argument/option Command
     *
     * @param InputInterface $p_input
     * @param OutputInterface $p_output
     */
    protected function initialize(InputInterface $p_input, OutputInterface $p_output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Execute command
     * Write log and start the import
     *
     * @param InputInterface $p_input
     * @param OutputInterface $p_output
     * @return bool|int|null
     * @throws \Exception
     */
    protected function execute(InputInterface $p_input, OutputInterface $p_output)
    {
        $startTime = microtime(true);

        // Start command
        $now = new \DateTime('now');
        $p_output->writeln([
            '',
            'Initialize default configuration in Lucca',
            'Start: ' . $now->format('d-m-Y H:i:s') . '',
            '-----------------------------------------------',
        ]);

        // Command logic stored here
        $this->initMediaBundle($p_input, $p_output);

        // Showing when the script is over
        $now = new \DateTime('now');
        $p_output->writeln([
            '',
            '-----------------------------------------------',
            'End: ' . $now->format('d-m-Y H:i:s') . '',
        ]);

        $finishTime = microtime(true);
        $elapsedTime = $finishTime - $startTime;
        $p_output->writeln([
            '',
            sprintf('<comment>[INFO] Import stored in database: Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
            '-----------------------------------------------',
        ]);

        return true;
    }

    /**
     * Import from file and create objects
     *
     * @param InputInterface $p_input
     * @param OutputInterface $p_output
     */
    protected function initMediaBundle(InputInterface $p_input, OutputInterface $p_output)
    {
        $em = $this->em;

        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        /** Search default Category - if not create it */
        $category = $em->getRepository(Category::class)->findDefaultCategory();

        if (!$category) {
            $category = new Category();
            $category->setName(Category::DEFAULT_NAME);
            $category->setDescription('Default category for all medias');

            $p_output->writeln('Create default Category and save it');
        } else
            $p_output->writeln('Default Category existing !');


        /** Search default Extension - if not create it and associated to Category */
        if ($category->hasExtension(Storager::DEFAULT_NAME) === false) {
            $extension = new Extension();
            $extension->setName(Extension::DEFAULT_NAME);
            $extension->setValue('*');
            $extension->setDescription('Default wildcard to store all medias by default');

            $category->addExtension($extension);
//            $em->persist($extension);
            $p_output->writeln('Create default Extension and save it');
        } else
            $p_output->writeln('Wildcard Extension existing !');


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
            $p_output->writeln('Create default Storager and save it');
        } else
            $p_output->writeln('Storager existing !');

        $em->persist($category);

        /** flush last items, detach all for doctrine and finish progress */
        $em->flush();
        $em->clear();
    }
}
