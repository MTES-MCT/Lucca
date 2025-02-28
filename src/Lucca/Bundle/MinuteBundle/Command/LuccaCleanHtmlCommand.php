<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LuccaCleanHtmlCommand
 *
 * @package Lucca\MinuteBundle\Command
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class LuccaCleanHtmlCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * Configure command parameters
     * Option : path of csv file
     */
    protected function configure()
    {
        $this
            ->setName('lucca:clean:html')
            ->setDescription('Clean all html of printed documents.')
            ->setHelp('Clean all html stored in database for printed documents from useless fonts.');
    }

    /**
     * Initialize var for process
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Execute command
     * Write log and start the clean
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);

        // Start command
        $now = new \DateTime();
        $output->writeln([
            '',
            'Clean html in Lucca DB',
            'Start: ' . $now->format('d-m-Y H:i:s') . '',
            '-----------------------------------------------',
        ]);

        // Clean html on DB via Doctrine ORM
        $this->cleanAllHtml($input, $output);

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
            sprintf('<comment>[INFO] Cleans html of folder in database: Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
            '-----------------------------------------------',
        ]);

        return true;
    }

    /***************************** Main function *****************************************************/

    /**
     * Clean all html of stored documents (remove useless fonts)
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function cleanAllHtml(InputInterface $input, OutputInterface $output)
    {
        $em = $this->em;

        /** Setup var that will be used to get range of folder and not all folder at one time */
        $rangeIds = 50;
        $startId = 0;
        $endId = $startId + $rangeIds;
        $nbFoldersCleaned = 0;

        /** Get folders in the range of ids */
        $folders = $em->getRepository('LuccaMinuteBundle:Folder')->findAllBetweenId($startId, $endId);

        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        /**
         * Step 1 : init var and progress
         * Define the size of record, the frequency for persisting the data and the current index of records
         */
        /** As long as we can find folders do the clean again on the new dataset */
        while (count($folders) > 0) {

            $batchSize = 10;
            $i = 1;
            $size = count($folders);
            $nbFoldersCleaned += count($folders);
            $progress = new ProgressBar($output, $size);
            $progress->start();

            /**
             * Step 2 : Clean all html of folders
             */
            foreach ($folders as $folder) {
                /** Store minute it will be useful for the next steps */
                $minute = $folder->getMinute();

                /** Folder -> ascertainment + details + violation
                 * Edition -> FolderVersion
                 */
                $this->getContainer()->get('lucca.utils.html_cleaner')->cleanHtmlFolder($folder);
                $em->persist($folder);

                /** Control :
                 *  Edition -> letterConvocation + letterAccess
                 */
                $control = $folder->getControl();
                $this->getContainer()->get('lucca.utils.html_cleaner')->cleanHtmlControl($control);
                $em->persist($control);

                /** Courier :
                 *  Edition -> letterJudicial + letterDDTM
                 *  Human Edition -> letterOffender
                 */
                $courier = $folder->getCourier();
                if ($courier) {
                    $this->getContainer()->get('lucca.utils.html_cleaner')->cleanHtmlCourier($courier);
                    $em->persist($courier);
                }

                /** Updating -> description */
                $updatings = $minute->getUpdatings();
                if (count($updatings) > 0) {
                    foreach ($updatings as $updating) {
                        $updating->setDescription($this->getContainer()->get('lucca.utils.html_cleaner')->removeAllFonts($updating->getDescription()));
                        $em->persist($updating);
                    }
                }

                /** Closure -> observation */
                $closure = $minute->getClosure();
                if ($closure) {
                    $closure->setObservation($this->getContainer()->get('lucca.utils.html_cleaner')->removeAllFonts($closure->getObservation()));
                    $em->persist($closure);
                }

                /** Decision :
                 * Decision -> dataEurope
                 * Expulsion -> comment
                 * Demolition -> comment
                 * Contradictory -> answer
                 * Commission -> restitution
                 */
                $decisions = $minute->getDecisions();
                if (count($decisions) > 0) {
                    foreach ($decisions as $decision) {
                        $this->getContainer()->get('lucca.utils.html_cleaner')->cleanHtmlDecision($decision);
                        $em->persist($decision);
                    }
                }


                /***************************** Flush and update progress bar *****************************************************/
                if (($i % $batchSize) === 0) {
                    $em->flush();
                    $progress->advance($batchSize);
                }
                $i++;
            }

            /** Clear $em before taking a new dataset */
            $em->clear();

            /** Get the next range of folders */
            $startId = $endId + 1;
            $endId = $startId + $rangeIds;

            $progress->finish();
            $output->writeln([
                '',
                sprintf('<comment>[INFO] %d Folders cleaned</comment>', $nbFoldersCleaned),
                '-----------------------------------------------',
            ]);

            /** Get next folders */
            $folders = $em->getRepository('LuccaMinuteBundle:Folder')->findAllBetweenId($startId, $endId);

        }

        /**
         * Step 3 : flush last items, detach all for doctrine and finish progress
         */
        $em->flush();
        $em->clear();
    }
}
