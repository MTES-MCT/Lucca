<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Lucca\Bundle\ChecklistBundle\Service\ChecklistService;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\FolderBundle\Service\NatinfService;
use Lucca\Bundle\ModelBundle\Service\ModelService;

class InitializationDepartmentCommand extends Command
{
    public function __construct(
        private readonly ChecklistService  $checklistService,
        private readonly EntityManagerInterface $em,
        private readonly ModelService      $modelService,
        private readonly NatinfService     $natinfService,
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
            ->setName('lucca:init:department')
            ->setDescription('Generate demo department');
    }

    /**
     * Execute action
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        // Start command
        $now = new DateTime('now');
        $output->writeln([
            '',
            'Generate application department',
            'Start: ' . $now->format('d-m-Y H:i:s'),
            '-----------------------------------------------',
        ]);

        // Command logic stored here
        $this->initDepartmentBundle();

        $finishTime = microtime(true);
        $elapsedTime = $finishTime - $startTime;
        $output->writeln([
            '',
            sprintf('<comment>[INFO] Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
            '-----------------------------------------------',
        ]);

        // Showing when the script is over
        $now = new DateTime('now');
        $output->writeln([
            '',
            '-----------------------------------------------',
            'End: ' . $now->format('d-m-Y H:i:s'),
        ]);

        return true;
    }

    protected function initDepartmentBundle(): void
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

        $demoDepartment = new Department();
        $demoDepartment->setName('Démo');
        $demoDepartment->setCode('demo');
        $demoDepartment->setDomainName('demo-lucca.local');

        $this->em->persist($demoDepartment);
        $this->em->flush();

        // Natinf creation from JSON data file
        $this->natinfService->createForDepartment($demoDepartment);

        // Checklist creation from JSON data file
        $this->checklistService->createForDepartment($demoDepartment);

        // Model creation from JSON data file
        $this->modelService->createForDepartment($demoDepartment);
    }
}
