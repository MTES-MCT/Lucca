<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ChecklistBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\ChecklistBundle\Entity\Checklist;
use Lucca\Bundle\ChecklistBundle\Form\ChecklistType;

#[Route(path: '/checklist')]
#[IsGranted('ROLE_ADMIN')]
class ChecklistController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of Checklist
     */
    #[Route(path: '/', name: 'lucca_checklist_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $checklists = $this->em->getRepository(Checklist::class)->findAll();

        return $this->render('@LuccaChecklist/Checklist/index.html.twig', [
            'checklists' => $checklists
        ]);
    }

    /**
     * Creates a new Checklist entity.
     */
    #[Route(path: '/new', name: 'lucca_checklist_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $checklist = new Checklist();

        $form = $this->createForm(ChecklistType::class, $checklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($checklist->getElements() as $element) {
                $checklist->addElement($element);
            }

            $this->em->persist($checklist);
            $this->em->flush();

            $this->addFlash('success', 'flashes.created_successfully');

            return $this->redirectToRoute('lucca_checklist_show', ['id' => $checklist->getId()]);
        }

        return $this->render('@LuccaChecklist/Checklist/new.html.twig', [
            'checklist' => $checklist,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Checklist entity.
     */
    #[Route(path: '/{id}', name: 'lucca_checklist_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Checklist $checklist): Response
    {
        $deleteForm = $this->createDeleteForm($checklist);

        return $this->render('@LuccaChecklist/Checklist/show.html.twig', [
            'checklist' => $checklist,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Checklist entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_checklist_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Checklist $checklist): Response
    {
        $deleteForm = $this->createDeleteForm($checklist);
        $editForm = $this->createForm(ChecklistType::class, $checklist);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($checklist->getElements() as $element) {
                $checklist->addElement($element);
            }

            $this->em->persist($checklist);
            $this->em->flush();

            $this->addFlash('info', 'flashes.updated_successfully');

            return $this->redirectToRoute('lucca_checklist_show', ['id' => $checklist->getId()]);
        }

        return $this->render('@LuccaChecklist/Checklist/edit.html.twig', [
            'checklist' => $checklist,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Checklist entity.
     */
    #[Route(path: '/{id}', name: 'lucca_checklist_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Checklist $checklist): Response
    {
        $form = $this->createDeleteForm($checklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($checklist);
            $this->em->flush();
        }

        $this->addFlash('danger', 'flashes.deleted_successfully');

        return $this->redirectToRoute('lucca_checklist_index');
    }

    /**
     * Creates a form to delete a Checklist entity.
     */
    private function createDeleteForm(Checklist $checklist): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_checklist_delete', ['id' => $checklist->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Checklist entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_checklist_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Checklist $checklist): Response
    {
        $checklist->toggle();
        $this->em->flush();

        $this->addFlash('info', 'flashes.toggled_successfully');

        return $this->redirectToRoute('lucca_checklist_index');
    }
}
