<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Command;

use Doctrine\ORM\EntityManagerInterface,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Helper\ProgressBar;

use Lucca\Bundle\SecurityBundle\Entity\LoginAttempt;
use Lucca\Bundle\SecurityBundle\Manager\LoginAttemptManager;

class UnbanCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoginAttemptManager    $loginAttemptManager
    )
    {
        /** Call the parent constructor */
        parent::__construct();
    }

    /**
     * Configure command parameters
     */
    protected function configure(): void
    {
        $this
            ->setName('lucca:security:unban')
            ->setDescription('Unban specific ip address.')
            ->setHelp('Unban a specific ip address - clear all attempts made with this ip.')
            ->addArgument('ip', InputArgument::REQUIRED, 'Give the specific ip to unban.');
    }

    /**
     * Execute command
     * Write log and start the import
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Start command
        $output->writeln([
            '-----------------------------------------------',
            'Lucca - Unban ip',
        ]);

        // Command logic stored here
        $this->unbanIp($input, $output);

        // Showing when the script is over
        $output->writeln([
            '',
            '-----------------------------------------------',
        ]);

        return 1;
    }

    /**
     * Unban specific ip with a time range configured in application
     */
    protected function unbanIp(InputInterface $p_input, OutputInterface $p_output): void
    {
        $em = $this->em;

        /** Unban ip with the LoginAttempt Manager -- all logic has been filled there and its return attempts unban */
        $results = $this->loginAttemptManager->approveIp($p_input->getArgument('ip'));
        $size = count($results);

        if ($size > 0) {
            /** Define the size of record, the frequency for persisting the data and the current index of records */
            /** Display all ip who was been unban */
            $batchSize = 5;
            $i = 1;
            $progress = new ProgressBar($p_output, $size);
            $progress->start();

            /** @var LoginAttempt $result */
            foreach ($results as $result) {

                $p_output->writeln([
                    '',
                    'LoginAttempt at ' . $result->getRequestedAt()->format('d-m-Y H:i:s') . ' with ip ' . $result->getRequestIp() . ' has been cleared.'
                ]);

                if (($i % $batchSize) === 0)
                    $progress->advance($batchSize);
                $i++;
            }

            /** flush last items, detach all for doctrine and finish progress */
            $em->flush();
            $em->clear();
            $progress->finish();
        } else {
            /** If no LoginAttempt has been found */
            $p_output->writeln([
                'No LoginAttempt founded with this ip.',
            ]);
        }
    }
}

