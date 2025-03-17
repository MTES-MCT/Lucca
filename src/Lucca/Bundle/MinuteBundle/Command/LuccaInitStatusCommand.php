<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Command;

use Lucca\Bundle\MinuteBundle\Manager\MinuteManager;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class LuccaInitStatusCommand
 *
 * @package Lucca\Bundle\MinuteBundle\Command
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class LuccaInitStatusCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MinuteManager $minuteManager,
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
            ->setName('lucca:init:status')
            ->setDescription('Init status of all minute.')
            ->setHelp('Init status of all minute.');
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
            'Init status for all minute',
            'Start: ' . $now->format('d-m-Y H:i:s') . '',
            '-----------------------------------------------',
        ]);

        // Init status on DB via Doctrine ORM
        $this->initStatus($input, $output);

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
    protected function initStatus(InputInterface $input, OutputInterface $output): void
    {
        $em = $this->em;

        /** Setup var that will be used to get range of minute and not all minute at one time */
        $rangeIds = 50;
        $startId = 0;
        $endId = $startId + $rangeIds;
        $nbMinutesUpdated = 0;

        $data = $this->em->getRepository(Minute::class)->findAllBetweenId($startId, $endId);

        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setMiddlewares([]);
        $progress = new ProgressBar($output, 0);

        while (count($data) > 0) {
            /**
             * Step 1 : init var and progress
             * Define the size of record, the frequency for persisting the data and the current index of records
             */
            $size = count($data);
            $nbMinutesUpdated += count($data);

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
            $file = $path . 'initStatus.txt';

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
             * Run service minute manager for each minute
             */
            /** @var Minute $minute */
            foreach ($data as $minute) {

                /** update and refresh status */
                $this->minuteManager->updateStatusAction($minute, true);
                $em->persist($minute);

                if (($i % $batchSize) === 0) {
                    $em->flush();
                    $progress->advance($batchSize);

                }
                $i++;
            }

            /** Clear $em before taking a new dataset */
            $em->flush();
            $em->clear();

            /** Get the next range of folders */
            $startId = $endId + 1;
            $endId = $startId + $rangeIds;

            $progress->finish();
            $output->writeln([
                '',
                sprintf('<comment>[INFO] %d Minutes status updated</comment>', $nbMinutesUpdated),
                '-----------------------------------------------',
            ]);

            /** Get next folders */
            $data = $this->em->getRepository(Minute::class)->findAllBetweenId($startId, $endId);
        }

        /**
         * Step 3 : flush last items, detach all for doctrine and finish progress
         */
        $em->flush();
        $em->clear();
        $progress->finish();
    }
}
