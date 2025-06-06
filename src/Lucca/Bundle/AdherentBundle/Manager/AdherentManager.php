<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Manager;

use Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Symfony\Component\HttpFoundation\RequestStack;

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

        $this->userManager->updateUser($adherent->getUser());

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
    public function checkPrerequisites(Adherent $adherent, Department $department, bool $isNew = false): bool
    {
        if ($isNew && $adherent->getUser()->getPlainPassword() === null) {
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'flash.adherent.missingPassword');
            return false;
        }

        $userExisting = $this->em->getRepository(User::class)->findOneBy(array(
            'email' => $adherent->getUser()->getEmail()
        ));

        if ($userExisting) {
            $adherentExisting = $this->em->getRepository(Adherent::class)->findOneBy(array(
                'user' => $userExisting, 'department' => $department->getId()
            ));

            if ($adherentExisting !== null && $adherentExisting->getUser() !== $adherent->getUser()) {
                $this->requestStack->getSession()->getFlashBag()->add('danger', 'flash.adherent.userAlreadyExist');

                return false;
            }

            $adherent->setUser($userExisting);
        }

        return true;
    }

    /**
     * Clone an adherent to use it in the new department
     */
    public function cloneAdherent(Adherent $adherent, Department $department): Adherent
    {
        $department = $this->em->getReference(Department::class, $department->getId());
        $newAdherent = clone $adherent;
        $newAdherent->setDepartment($department);
        $newAdherent->setService(null);
        $newAdherent->setTown(null);
        $newAdherent->setIntercommunal(null);

        if ($adherent->getService()) {
            $newService = clone $adherent->getService();
            $newService->setDepartment($department);
            $this->em->persist($newService);
            $newAdherent->setService($newService);
        }

        $this->em->persist($newAdherent);
        $this->em->flush();

        return $newAdherent;
    }
}
