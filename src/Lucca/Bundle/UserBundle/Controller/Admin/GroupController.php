<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lucca\Bundle\UserBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\UserBundle\Entity\Group;
use Lucca\Bundle\UserBundle\Form\Type\GroupFormType;

#[IsGranted('ROLE_USER')]
#[Route(path: '/group')]
class GroupController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,)
    {
    }

    /**
     * Show all groups.
     */
    #[Route(path: '/', name: 'lucca_user_group_index', methods: ['GET'])]
    public function indexAction(): Response
    {
        $groups = $this->em->getRepository(Group::class)->findAll();

        return $this->render('@LuccaUser/Group/index.html.twig', [
            'groups' => $groups,
        ]);
    }

    /**
     * Show one group.
     */
    #[Route(path: '/{id}', name: 'lucca_user_group_show', methods: ['GET'])]
    public function showAction(Group $group): Response
    {
        $deleteForm = $this->createDeleteForm($group);
        return $this->render('@LuccaUser/Group/show.html.twig', [
            'group' => $group,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Creates a new Group entity
     */
    #[Route(path: '/new', name: 'lucca_user_group_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request): Response
    {
        $group = new Group();

        $form = $this->createForm(GroupFormType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'flashes.created_successfully');

            $this->em->persist($group);
            $this->em->flush();

            return $this->redirectToRoute('lucca_user_group_show', ['id' => $group->getId()]);
        }

        return $this->render('@LuccaUser/Group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit one group, show the edit form.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_user_group_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Group $group): Response
    {
        $deleteForm = $this->createDeleteForm($group);

        $editForm = $this->createForm(GroupFormType::class, $group);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->addFlash('success', 'flashes.updated_successfully');

            return $this->redirectToRoute('lucca_user_group_show', ['id' => $group->getId()]);
        }

        return $this->render('@LuccaUser/Group/edit.html.twig', [
            'group' => $group,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Delete one group.
     */
    #[Route(path: '/{id}', name: 'lucca_user_group_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, Group $group): Response
    {
        $form = $this->createDeleteForm($group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($group);
            $this->em->flush();
        }

        $this->addFlash('success', 'flashes.deleted_successfully');

        return $this->redirectToRoute('lucca_user_group_index');
    }

    /**
     * Creates a form to delete a Group entity.
     */
    private function createDeleteForm(Group $group): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_user_group_delete', ['id' => $group->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
