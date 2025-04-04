<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;

use Lucca\Bundle\FolderBundle\Entity\{CourierEdition, CourierHumanEdition, FolderEdition};
use Lucca\Bundle\MediaBundle\Utils\PathFormatter;
use Lucca\Bundle\MinuteBundle\Entity\ControlEdition;

class FixMediaCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PathFormatter $pathFormatter,
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
            ->setName('lucca:fix:media')
            ->setDescription('Fix html manual edit that have a local path saved.')
            ->setHelp('Fix all media that are save in html with the path given')
            ->addOption(
                'path', null, InputOption::VALUE_REQUIRED,
                'Give the path that need to be clean.'
            )
            ->addOption(
                'defaultFileName', null, InputOption::VALUE_REQUIRED,
                'Give the name of the default media to use if media can\'t be found.'
            )
            ->addOption(
                'defaultId', null, InputOption::VALUE_REQUIRED,
                'Give the id of the default media to use if media can\'t be found.'
            );
    }

    /**
     * Execute command
     * Write log and start the migration
     *
     * @throws \Exception
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
        // Catch path option
        $optionPath = $input->getOption('path');
        $defaultFileName = $input->getOption('defaultFileName');
        $defaultId = $input->getOption('defaultId');
        $this->reformatContent($output, $optionPath, $defaultFileName, $defaultId);

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
            sprintf('<comment>[INFO] Migration ended: Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
            '-----------------------------------------------',
        ]);

        return true;
    }

    /**
     * This function is used to reformat all old file path
     */
    protected function reformatContent($p_output, $p_optionPath, $p_defaultFileName, $p_defaultId): void
    {
        /******************************* Editions ******************************************************************/
        /** Get services used for the next step */
        $em = $this->em;
        $batchSize = 10;
        $i = 1;
        $oldPath = $p_optionPath;

        /** Get all control editions */
        $editions = $em->getRepository(ControlEdition::class)->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($editions));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image-jpg */
        foreach ($editions as $edition) {
            if ($edition->getLetterConvocation()) {
                $edition->setLetterConvocation($this->pathFormatter->formatBrokenPath($edition->getLetterConvocation(), null, $oldPath, $p_defaultFileName, $p_defaultId));
            }
            if ($edition->getLetterAccess()) {
                $edition->setLetterAccess($this->pathFormatter->formatBrokenPath($edition->getLetterAccess(), null, $oldPath, $p_defaultFileName, $p_defaultId));
            }
            $em->persist($edition);

            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();

        $p_output->writeln(sprintf('<comment>[INFO] End of control edition migration</comment>'));

        /********* Get all courrier editions **********/
        $editions = $em->getRepository(CourierEdition::class)->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($editions));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
        foreach ($editions as $edition) {
            if ($edition->getLetterDdtm()) {
                $edition->setLetterDdtm($this->pathFormatter->formatBrokenPath($edition->getLetterDdtm(), null, $oldPath, $p_defaultFileName, $p_defaultId));
            }
            if ($edition->getLetterJudicial()) {
                $edition->setLetterJudicial($this->pathFormatter->formatBrokenPath($edition->getLetterJudicial(), null, $oldPath, $p_defaultFileName, $p_defaultId));
            }
            $em->persist($edition);

            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();
        $p_output->writeln(sprintf('<comment>[INFO] End of courier edition migration</comment>'));

        /************* Get all folder editions *************/
        $editions = $em->getRepository(FolderEdition::class)->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($editions));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
        foreach ($editions as $edition) {
            if ($edition->getFolderVersion()) {
                $edition->setFolderVersion($this->pathFormatter->formatBrokenPath($edition->getFolderVersion(), null, $oldPath, $p_defaultFileName, $p_defaultId));
                $em->persist($edition);
            }
            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();
        $p_output->writeln(sprintf('<comment>[INFO] End of folder edition migration</comment>'));

        /*********** Get all courier human editions *************/
        $editions = $em->getRepository(CourierHumanEdition::class)->findAll();

        /** Create progress bar */
        $progress = new ProgressBar($p_output, count($editions));
        $progress->start();

        /** For each edition change old media path to use the new one */
        /** Example : /media/2020/11/image.jpg -> /media/show/image.jpg */
        foreach ($editions as $edition) {
            if ($edition->getLetterOffender()) {
                $edition->setLetterOffender($this->pathFormatter->formatBrokenPath($edition->getLetterOffender(), null, $oldPath, $p_defaultFileName, $p_defaultId));
            }
            if ($edition->getLetterOffenderEdited()) {
                $edition->setLetterOffenderEdited($this->pathFormatter->formatBrokenPath($edition->getLetterOffenderEdited(), null, $oldPath, $p_defaultFileName, $p_defaultId));
            }
            $em->persist($edition);
            /** Update progress bar */
            if (($i % $batchSize) === 0) {
                $em->flush();
                $progress->advance($batchSize);
            }
            $i++;
        }
        $em->flush();
        $em->clear();
        $p_output->writeln(sprintf('<comment>[INFO] End of courier human edition migration</comment>'));

        /******************************* Html fields *****************************************************************/
    }
}
