<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Controller\Profile;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Form\ProfileType;
use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\UserBundle\Manager\UserManager;

#[Route(path: '/my-profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserManager            $userManager
    )
    {
    }

    /**
     * Finds and displays a Adherent entity.
     */
    #[Route(path: '/', name: 'lucca_adherent_profile_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showProfileAction(): Response
    {
        /** Find Adherent by connected User */
        $user = $this->getUser();
        $adherent = $this->em->getRepository(Adherent::class)->findOneBy([
            'user' => $user
        ]);

        return $this->render('@LuccaAdherent/Profile/show.html.twig', [
            'adherent' => $adherent,
        ]);
    }

    /**
     * Displays a form to edit an existing Adherent entity.
     */
    #[Route(path: '/edit', name: 'lucca_adherent_profile_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function editAction(Request $request): Response
    {
        /** Find Adherent by connected User */
        $user = $this->getUser();
        $adherent = $this->em->getRepository(Adherent::class)->findOneBy([
            'user' => $user
        ]);

        $editForm = $this->createForm(ProfileType::class, $adherent);

        /** Init unmapped field - email */
        $editForm->get('email')->setData($adherent->getUser()->getEmail());

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** If user with this email already exist */
            $userExisting = $this->em->getRepository(User::class)->findOneBy([
                'email' => $editForm->get('email')->getData()
            ]);
            if ($userExisting and $userExisting !== $adherent->getUser()) {
                $this->addFlash('warning', 'flash.adherent.userAlreadyExist');

                return $this->render('@LuccaAdherent/Profile/edit.html.twig', [
                    'adherent' => $adherent,
                    'edit_form' => $editForm->createView(),
                ]);
            }

            /** If no other user exist */
            $user = $adherent->getUser();
            $user->setName($adherent->getName());
            $user->setEmail($editForm->get('email')->getData());

            $this->userManager->updateUser($user);
            $this->em->persist($adherent);
            $this->em->flush();

            $this->addFlash('info', 'flash.adherent.updatedSuccessfully');

            return $this->redirectToRoute('lucca_adherent_profile_show');
        }

        return $this->render('@LuccaAdherent/Profile/edit.html.twig', [
            'adherent' => $adherent,
            'edit_form' => $editForm->createView(),
        ]);
    }
}
