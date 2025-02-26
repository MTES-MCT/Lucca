<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\CoreBundle\Utils\Canonalizer;

readonly class UserManager
{
    public function __construct(
        private EntityManagerInterface      $em,
        private UserPasswordHasherInterface $passwordHasher,
        private Canonalizer                 $canonalizer,
    )
    {
    }

    /**
     * Create a specific User with params
     */
    public function createUser(string $p_username, string $p_email, string $p_plainPassword, array $p_roles): User|bool
    {
        /** Step 1 - Search if user still existing */
        $user = $this->em->getRepository(User::class)->loadUserByIdentifier($p_username, $p_email);

        /** If User has been found with this email - skip it */
        if (!$user) {
            $user = new User();

            $user->setIsEnabled(true);
            $user->setUsername($p_username);
            $user->setUsernameCanonical($this->canonalizer->slugify($p_username));
            $user->setEmail($p_email);
            $user->setEmailCanonical($this->canonalizer->slugify($p_email));

            $user->setRoles($p_roles);
            $user->setPlainPassword($p_plainPassword);
            $this->changePasswordUser($user, $p_plainPassword);

            return $user;
        }

        return false;
    }

    /**
     * Change password for User
     */
    public function changePasswordUser(User $user, string $plainPassword): User
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        ));

        $user->eraseCredentials();

        return $user;
    }

    /**
     * Update User entity and hash this password
     */
    public function updateUser(User|UserInterface $user): bool
    {
        if ($user->getPlainPassword() !== null) {
            $this->changePasswordUser($user, $user->getPlainPassword());
        }

        $user->setUsernameCanonical($this->canonalizer->slugify($user->getUsername()));
        $user->setEmailCanonical($this->canonalizer->slugify($user->getEmail()));

        return true;
    }

    /**
     * Change password for User
     */
    public function refreshLastConnection(User $user): User
    {
        $user->eraseCredentials();

        return $user;
    }

    /**
     * Activate this User
     */
    public function activateUser(User $user): User
    {
        $user->setIsEnabled(true);

        return $user;
    }

    /**
     * Deactivate this User
     */
    public function deactivateUser(User $user): User
    {
        $user->setIsEnabled(false);

        return $user;
    }

    public function getName(): string
    {
        return 'lucca.user.manager';
    }
}
