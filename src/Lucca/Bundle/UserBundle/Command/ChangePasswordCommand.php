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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\UserBundle\Manager\UserManager;
use Lucca\Bundle\UserBundle\Repository\UserRepository;

/**
 * call with : lucca:user:change-password
 */
class ChangePasswordCommand extends Command
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
            ->setName('lucca:user:change-password')
            ->setDescription('Change password of User.')
            ->setHelp('Search a User by username or email to change his password.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username or email given of User.')
            ->addArgument('password', InputArgument::REQUIRED, 'Password attributed to User.');
    }

    /**
     * Execute this command
     * Write log and start the import
     * TODO log the result
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(array(
            '-----------------------------------------------',
            'Lucca -> Change Password',
        ));

        /** Step 1 - Search if user still existing */
        /** @var UserRepository $repository */
        $repository = $this->em->getRepository(User::class);
        $user = $repository->findByUsernameOrEmail($input->getArgument('username'));

        /** Step 2 - change password */
        if ($user && $user instanceof User) {
            $this->userManager->changePasswordUser($user, $input->getArgument('password'));

            $output->writeln(array(
                'User -> ' . $user->getUsername() . ' have a new password.',
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

