<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Finder;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\UserBundle\Entity\User;

readonly class AdherentFinder
{
    public function __construct(
        private EntityManagerInterface $em,
        private TokenStorageInterface  $tokenStorage,
    )
    {
    }

    /**
     * Who is connected ? Return a Adherent entity
     */
    public function whoAmI(): Adherent
    {
        /** @var $user User -- Get current user connected */
        $user = $this->tokenStorage->getToken()->getUser();

        /** Find adherent linked to this User */
        $adherent = $this->em->getRepository(Adherent::class)->findOneByUser($user);

        if (!$adherent) {
            throw new AuthenticationException('No adherent entity has been found');
        }

        return $adherent;
    }

    public function getName(): string
    {
        return 'lucca.finder.adherent';
    }
}
