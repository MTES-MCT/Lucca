<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\ModelBundle\Entity\{Page, Model};

class InitializationCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
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
            ->setName('lucca:init:model')
            ->setDescription('Initialize ModelBundle with default params.')
            ->setHelp('Initialize ModelBundle with simple model');
    }

    /**
     * Execute command
     * Write log and start the import
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        // Start command
        $now = new DateTime('now');
        $output->writeln([
            '',
            'Initialize default configuration in Lucca',
            'Start: ' . $now->format('d-m-Y H:i:s'),
            '-----------------------------------------------',
        ]);

        // Command logic stored here
        $this->initModelBundle($input, $output);

        // Showing when the script is over
        $now = new DateTime('now');
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
    protected function initModelBundle(InputInterface $input, OutputInterface $output): void
    {
        // Turning off doctrine default logs queries for saving memory
        $connection = $this->em->getConnection();

        if (method_exists($connection, 'getConfiguration')) {
            $config = $connection->getConfiguration();

            if (method_exists($config, 'setSQLLogger')) {
                // Ne rien logger du tout
                $config->setSQLLogger(null);
            }
        }

        foreach (Model::getDocumentsChoice() as $document) {
            /** Search if model exist for this document - Do request in loop because we know the end of the loop */
            $model = $this->em->getRepository(Model::class)->findByDocument($document);

            if (!$model) {
                $model = new Model();

                $model->setName($model->getLogName() . ' ' . $this->translator->trans($document, [], 'ModelBundle'));
                $model->setType(Model::TYPE_ORIGIN);
                $model->setLayout(Model::LAYOUT_SIMPLE);
                $model->setDocuments([$document]);

                $page = new Page();
                $page->setHeaderSize(20);
                $page->setFooterSize(20);
                $page->setLeftSize(20);
                $page->setRightSize(20);

                $model->setRecto($page);

                $this->em->persist($model);
                $output->writeln([
                    'Model created for document',
                    $document,
                ]);
            } else {
                $output->writeln([
                    'Default model already exist for',
                    $document,
                ]);
            }

            $this->em->persist($model);
        }

        /** flush last items, detach all for doctrine and finish progress */
        $this->em->flush();
        $this->em->clear();
    }
}
