<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Command;

use DateTime;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Lucca\Bundle\settingBundle\Service\SettingGenerator;

class GenerateSettingsCommand extends Command
{
    public function __construct(
        private readonly SettingGenerator $settingGenerator,
    ) {
        parent::__construct();
    }

    /**
     * Configure command parameters
     */
    protected function configure(): void
    {
        $this
            ->setName('lucca:init:settings')
            ->setDescription('Generate new settings and update existing settings.');
    }

    /**
     * Execute action
     *
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        // Start command
        $now = new DateTime('now');
        $output->writeln([
            '',
            'Generate / Update application settings',
            'Start: ' . $now->format('d-m-Y H:i:s'),
            '-----------------------------------------------',
        ]);

        $aDictionary = $this->settingGenerator->getCachedSettings(true);

        // Success
        $output->writeln(sprintf('%d settings generated or updated.', count($aDictionary)));

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
}
