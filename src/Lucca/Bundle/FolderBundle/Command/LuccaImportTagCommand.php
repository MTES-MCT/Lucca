<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Command;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Proposal;
use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class LuccaImportTagCommand
 *
 * @package Lucca\MinuteBundle\Command
 * @author Terence <terence@numeric-wave.tech>
 */
class LuccaImportTagCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var Filesystem
     */
    private $file;

    /**
     * Configure command parameters
     * Option : path of csv file
     */
    protected function configure()
    {
        $this
            ->setName('lucca:import:tag')
            ->setDescription('Import tags csv files.')
            ->setHelp('Import a csv file for add tag in Lucca application.')
            ->addOption(
                'path', null, InputOption::VALUE_REQUIRED,
                'Give the path of tag csv file.'
            );
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
        $fs = new Filesystem();

        // Catch path option
        $optionPath = $input->getOption('path');

        // Test if file provide by option
        if ($optionPath) {
            if ($fs->exists($optionPath))
                $this->file = $optionPath;
        }

        // If file don't exist, or option is not given
        if ($this->file === null)
            $this->file = $this->getContainer()->getParameter('path.import') . $this->getContainer()->getParameter('file.tag');

        // If file doesn't exist
        if (!$fs->exists($this->file))
            throw new \Exception('File can not be empty');
    }

    /**
     * Execute command
     * Write log and start the import
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
            'Import Tag for Lucca\'s file',
            'Start: ' . $now->format('d-m-Y H:i:s') . '',
            '-----------------------------------------------',
        ]);

        // Importing CSV on DB via Doctrine ORM
        $this->import($input, $output, $this->file);

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
     * @param $file
     */
    protected function import(InputInterface $input, OutputInterface $output, $file)
    {
        $em = $this->em;
        $data = $this->get($input, $output, $file);

        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

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
         * Step 2 : Processing on each row of data
         * Parse data and create object for each one
         * Parse tag and create none exist tag
         */
        foreach ($data as $row) {

            $tag_element = ucfirst(trim(utf8_encode($row['num'])));
            $tag = $em->getRepository('LuccaMinuteBundle:Tag')->findOneBy(array(
                'num' => $tag_element
            ));

            if (!is_object($tag) && strlen(trim($tag_element)) != 0) {
                $tag = new Tag();
                $tag->setNum($tag_element);
                $tag->setName(ucfirst(trim(utf8_encode($row['tag']))));
            }

            if (utf8_encode($row['category']) == 1)
                $tag->setCategory(Tag::CATEGORY_NATURE);
            elseif (utf8_encode($row['category']) == 2)
                $tag->setCategory(Tag::CATEGORY_TOWN);

            $this->addNewProposal($tag, ucfirst(utf8_encode($row['sentence1'])));
            $this->addNewProposal($tag, ucfirst(utf8_encode($row['sentence2'])));
            $this->addNewProposal($tag, ucfirst(utf8_encode($row['sentence3'])));
            $this->addNewProposal($tag, ucfirst(utf8_encode($row['sentence4'])));
            $this->addNewProposal($tag, ucfirst(utf8_encode($row['sentence5'])));
            $this->addNewProposal($tag, ucfirst(utf8_encode($row['sentence6'])));
            $this->addNewProposal($tag, ucfirst(utf8_encode($row['sentence7'])));

            $em->persist($tag);

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

    /**
     * Return data array provided by csv file
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $fileName
     * @return mixed
     */
    protected function get(InputInterface $input, OutputInterface $output, $fileName)
    {
        $converter = $this->getContainer()->get('lucca.converter.csv_array');
        $data = $converter->convert($fileName, ';');

        return $data;
    }

    /**
     * @param Tag $tag
     * @param $sentence
     * @return bool|Proposal
     */
    protected function addNewProposal(Tag $tag, $sentence)
    {
        if ($sentence) {
            $proposal = $this->em->getRepository('LuccaMinuteBundle:Proposal')->findOneBy(array(
                'sentence' => $sentence
            ));

            if (!is_object($proposal)) {
                $proposal = new Proposal();
                $proposal->setEnabled(true);
                $proposal->setCreatedAt(new \Datetime());
                $proposal->setTag($tag);
                $proposal->setSentence($sentence);
                $this->em->persist($proposal);

                return $proposal;
            }
        }

        return false;
    }
}
