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

use Lucca\Bundle\MinuteBundle\Entity\Plot;

/**
 * Class LuccaInitLocationFromCommand
 *
 * @package Lucca\Bundle\MinuteBundle\Command
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class LuccaInitLocationFromCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
        parent::__construct();
    }

    /**
     * Configure command parameters
     */
    protected function configure(): void
    {
        $this
            ->setName('lucca:init:locationFrom')
            ->setDescription('Init field to know were the location come from.')
            ->setHelp('Init field to know were the location come from for plot with null value for it it will be initialize to address.');
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
            'Init locationFrom for Lucca\Bundle\'s file',
            'Start: ' . $now->format('d-m-Y H:i:s') . '',
            '-----------------------------------------------',
        ]);

        // Init Location from on DB via Doctrine ORM
        $this->InitLocationFrom($input, $output);

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
            sprintf('<comment>[INFO] Initialisation stored in database: Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
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
    protected function InitLocationFrom(InputInterface $input, OutputInterface $output): void
    {
        $em = $this->em;

        $data = $em->getRepository(Plot::class)->findAllWithoutLocationFrom();

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
         * Step 3 : Processing on each row of data
         * For each plot init location from and persist
         */
        /** @var Plot $plot */
        foreach ($data as $plot) {

            /** Set location from with address */
            $plot->setLocationFrom(PLOT::LOCATION_FROM_ADDRESS);

            $em->persist($plot);

            if (($i % $batchSize) === 0) {
                $em->flush();
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
