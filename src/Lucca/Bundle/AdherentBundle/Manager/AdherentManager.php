<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Manager;

use Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Generator\CodeGenerator;
use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\UserBundle\Manager\UserManager;
use Lucca\Bundle\UserBundle\Entity\User;

readonly class AdherentManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack           $requestStack,
        private UserManager            $userManager,
        private CodeGenerator          $codeGenerator,
    )
    {
    }

    /**
     * Init a new Adherent and sync User associated
     *
     * @throws Exception
     */
    public function initNewAdherent(Adherent $adherent): Adherent
    {
        $this->synchronizeUserEntity($adherent, $adherent->getUser());
        $this->manageLogo($adherent);

        /** Generate Username user */
        $adherent->getUser()->setUsername($this->codeGenerator->generate($adherent));

        $this->userManager->updateUser($adherent->getUser(), false);

        return $adherent;
    }

    /**
     * Edit an Adherent and sync User associated
     */
    public function editAdherent(Adherent $adherent): Adherent
    {
        $this->synchronizeUserEntity($adherent, $adherent->getUser());
        $this->manageLogo($adherent);

        $this->userManager->updateUser($adherent->getUser(), false);

        return $adherent;
    }

    /**
     * Manage Logo associated to Adherent
     */
    private function manageLogo(Adherent $p_adherent): ?Media
    {
        /** @var Media $logo */
        $logo = $p_adherent->getLogo();

        if ($logo && $logo->getId() !== null && $logo->getName() === null) {
            $p_adherent->setLogo(null);

            try {
                $this->em->remove($logo);
            } catch (ORMException $ORMException) {
                echo 'ORMException has been thrown - Adherent logo ' . $ORMException->getMessage();
            }

            return null;
        }

        return $logo;
    }

    /**
     * Synchronization between Staff and User
     */
    private function synchronizeUserEntity(Adherent $adherent, User $user): void
    {
        /** Copy name/enabled Staff on User fields */
        $user->setName($adherent->getName());
        $user->setEnabled($adherent->getEnabled());
    }

    /**
     * Check if an Adherent entity was correctly configured
     */
    public function checkPrerequisites(Adherent $adherent): bool
    {
        $userExisting = $this->em->getRepository(User::class)->findOneBy(array(
            'email' => $adherent->getUser()->getEmail()
        ));

        if ($userExisting !== null && $userExisting !== $adherent->getUser()) {
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'flash.adherent.userAlreadyExist');
        }

        /** Return true if a dangerous message has been found in Session */
        return !$this->requestStack->getSession()->getFlashBag()->has('danger');
    }

    public function getName(): string
    {
        return 'lucca.manager.adherent';
    }
}
