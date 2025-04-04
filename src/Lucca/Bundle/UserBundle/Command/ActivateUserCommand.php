<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Command;

use Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\UserBundle\Manager\UserManager;
use Lucca\Bundle\UserBundle\Repository\UserRepository;

/**
 * call with : lucca:user:activate
 */
class ActivateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserManager            $userManager
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
            ->setName('lucca:user:activate')
            ->setDescription('Activate a specific User.')
            ->setHelp('Search a User by username or email to activate it.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username or email given of User.');
    }

    /**
     * Execute this command
     * Write log and start the import
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(array(
            '-----------------------------------------------',
            'Lucca -> Activate User',
        ));

        /** Step 1 - Search if user still existing */
        /** @var UserRepository $repository */
        $repository = $this->em->getRepository(User::class);
        $user = $repository->findByUsernameOrEmail($input->getArgument('username'));

        /** Step 2 - Activate it */
        if ($user && $user instanceof User) {
            $this->userManager->activateUser($user);

            $output->writeln(array(
                'User -> ' . $user->getUsername() . ' has been activated.',
            ));

            try {
                $this->em->persist($user);
                $this->em->flush();
            } catch (ORMException $e) {
                $output->writeln('User cannot be persisted on specific Command - ' . $user->getEmail() . ' - error -> ' . $e->getMessage());

                return Command::FAILURE;
            }
        }

        $output->writeln(array(
            '-----------------------------------------------',
        ));

        return Command::SUCCESS;
    }
}

