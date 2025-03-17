<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use Lucca\Bundle\CoreBundle\Service\GeoLocator;
use Lucca\Bundle\MinuteBundle\Entity\Plot;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class LuccaInitGeocodesCommand
 *
 * @package Lucca\Bundle\MinuteBundle\Command
 * @author Terence <terence@numeric-wave.tech>
 */
class LuccaInitGeocodesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GeoLocator $geoLocator,
        private readonly KernelInterface $kernel
    )
    {
        parent::__construct();
    }

    /**
     * @var Filesystem
     */
    private $file;

    /**
     * Configure command parameters
     * Option : path of csv file
     */
    protected function configure(): void
    {
        $this
            ->setName('lucca:init:geocode')
            ->setDescription('Init geo codes of all plots.')
            ->setHelp('Init geo code (latitude and longitude) of all plots if they haven\'t already one.');
    }

    /**
     * Initialize var for process
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $fs = new Filesystem();
    }

    /**
     * Execute command
     * Write log and start the import
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        // Start command
        $now = new \DateTime();
        $output->writeln([
            '',
            'Init geo codes for Lucca\Bundle\'s file',
            'Start: ' . $now->format('d-m-Y H:i:s') . '',
            '-----------------------------------------------',
        ]);

        // Init geo code on DB via Doctrine ORM
        $this->initGeocodes($input, $output);

        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln([
            '',
            '-----------------------------------------------',
            'End: ' . $now->format('d-m-Y H:i:s') . '',
        ]);

        $finishTime = microtime(true);
        $elapsedTime = $finishTime - $startTime;
        $output->writeln([
            '',
            sprintf('<comment>[INFO] Import stored in database: Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
            '-----------------------------------------------',
        ]);

        return true;
    }

    /**
     * Import from file and create objects
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initGeocodes(InputInterface $input, OutputInterface $output): void
    {
        $em = $this->em;

        $data = $this->em->getRepository(Plot::class)->findAllWithoutGeocode();

        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setMiddlewares([]);

        /**
         * Step 1 : init var and progress
         * Define the size of record, the frequency for persisting the data and the current index of records
         */
        $size = count($data);
        $batchSize = 20;
        $i = 1;
        $progress = new ProgressBar($output, $size);
        $progress->start();


        /**
         * Step 2 : create log file
         * to store errors returned
         * from google Api
         */
        $path = $this->kernel->getProjectDir() . '/public/assets/log/';
        $file = $path . 'initGeocodes.txt';

        $filesystem = new Filesystem();

        /** If the folder does not exist then create it. */
        if (!$filesystem->exists($path)) {
            mkdir($path);
        }
        if ($filesystem->exists($file)) {
            $filesystem->remove($file);
        }
        touch($file);

        /**
         * Step 3 : Processing on each row of data
         * Parse data and create object for each one
         * Parse tag and create none exist tag
         */
        /** @var Plot $plot */
        foreach ($data as $plot) {

            /** Call geo locator service to set latitude and longitude of plot */
            $this->geoLocator->addGeocodeFromAddress($plot);

            if ($plot->getLatitude() === NULL || $plot->getLongitude() === NULL ) {
                $filesystem->appendToFile($file, 'ID : ' . $plot->getId() . ', Address : ' . $plot->getAddress() . "\r\n");
            }

            $em->persist($plot);

            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear();
                $progress->advance($batchSize);
            }
            $i++;
        }

        /**
         * Step 3 : flush last items, detach all for doctrine and finish progress
         */
        $em->flush();
        $em->clear();
        $progress->finish();
    }
}
