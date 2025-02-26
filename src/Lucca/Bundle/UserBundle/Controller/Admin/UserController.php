<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use FOS\UserBundle\Doctrine\UserManager;

use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\UserBundle\Form\UserType;

#[Route(path: '/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserManager $userManager,
    )
    {
    }

    /**
     * User list
     */
    #[Route(path: '/', name: 'lucca_user_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->render('@LuccaUser/User/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Creates a new User entity
     */
    #[Route(path: '/new', name: 'lucca_user_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->updateUser($user);

            $this->addFlash('success', 'flashes.created_successfully');

            return $this->redirectToRoute('lucca_user_show', ['id' => $user->getId()]);
        }

        return $this->render('@LuccaUser/User/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a User entity.
     */
    #[Route(path: '/{id}', name: 'lucca_user_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(User $user): Response
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('@LuccaUser/User/show.html.twig', [
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing User entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, User $user): Response
    {
        $deleteForm = $this->createDeleteForm($user);

        $editForm = $this->createForm(UserType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userManager->updateUser($user);

            $this->addFlash('success', 'user.updated_successfully');

            return $this->redirectToRoute('lucca_user_show', ['id' => $user->getId()]);
        }

        return $this->render('@LuccaUser/User/edit.html.twig', [
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a User entity.
     */
    #[Route(path: '/{id}', name: 'lucca_user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, User $user): Response
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->deleteUser($user);
        }

        $this->addFlash('success', 'user.deleted_successfully');

        return $this->redirectToRoute('lucca_user_index');
    }

    /**
     * Creates a form to delete a User entity.
     */
    private function createDeleteForm(User $user): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_user_delete', ['id' => $user->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a User entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_user_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(User $user): Response
    {
        if ($user->isEnabled()) {
            $user->setEnabled(false);
        } else {
            $user->setEnabled(true);
        }

        $this->userManager->updateUser($user);

        $this->addFlash('info', 'flashes.toggled_successfully');

        return $this->redirectToRoute('lucca_user_index');
    }
}
