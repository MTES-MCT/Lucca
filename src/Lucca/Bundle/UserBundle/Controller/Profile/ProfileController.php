<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Controller\Profile;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\UserBundle\Form\Profile\{ProfileType, ProfilePasswordType};
use Lucca\Bundle\UserBundle\Manager\UserManager;

#[Route(path: '/my-profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly UserManager            $userManager,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * Displays a form to change a password for an existing User.
     */
    #[Route(path: '/change-password', name: 'lucca_user_profile_changePassword', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function changePasswordAction(Request $request): Response
    {
        /** Get current user connected */
        $user = $this->getUser();

        $form = $this->createForm(ProfilePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Update User and hash password */
            $this->userManager->updateUser($user);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'flash.user.passwordUpdatedSuccessfully');

            return $this->redirectToRoute('lucca_adherent_profile_show');
        }

        return $this->render('@LuccaUser/Profile/changePassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
