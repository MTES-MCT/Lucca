<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Controller\MyProfile;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\UserBundle\Form\MyProfile\{ProfileType, ProfilePasswordType};
use Lucca\Bundle\UserBundle\Manager\UserManager;


#[Route(path: '/user/my-profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * Finds and displays a User profile.
     */
    #[Route(path: '/', name: 'lucca_user_myProfile_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function myProfileAction(): Response
    {
        /** Get current user connected */
        $user = $this->getUser();

        return $this->render('@LuccaUser/MyProfile/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Displays a form to edit an existing User profile.
     */
    #[Route(path: '/edit', name: 'nw_user_myProfile_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function editAction(Request $p_request): Response
    {
        /** Get current user connected */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($p_request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Update User and hash password */
            $this->userManager->updateUser($user);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'flash.user.updatedSuccessfully');

            return $this->redirectToRoute('lucca_user_myProfile_show');
        }

        return $this->render('@LuccaUser/MyProfile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to change a password for an existing User.
     */
    #[Route(path: '/change-password', name: 'lucca_user_myProfile_changePassword', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function changePasswordAction(Request $p_request): Response
    {
        /** Get current user connected */
        $user = $this->getUser();

        $form = $this->createForm(ProfilePasswordType::class, $user);
        $form->handleRequest($p_request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Update User and hash password */
            $this->userManager->updateUser($user);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'flash.user.passwordUpdatedSuccessfully');

            return $this->redirectToRoute('lucca_user_myProfile_show');
        }

        return $this->render('@LuccaUser/MyProfile/changePassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
