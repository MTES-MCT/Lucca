<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\ParameterBundle\Entity\Tribunal;
use Lucca\Bundle\ParameterBundle\Form\TribunalType;

#[Route(path: '/tribunal')]
#[IsGranted('ROLE_ADMIN')]
class TribunalController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of Tribunal
     */
    #[Route(path: '/', name: 'lucca_tribunal_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $tribunals = $this->em->getRepository(Tribunal::class)->findAll();

        return $this->render('@LuccaParameter/Tribunal/index.html.twig', [
            'tribunals' => $tribunals
        ]);
    }

    /**
     * Creates a new Tribunal entity.
     */
    #[Route(path: '/new', name: 'lucca_tribunal_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $tribunal = new Tribunal();

        $form = $this->createForm(TribunalType::class, $tribunal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($tribunal);
            $this->em->flush();

            $this->addFlash('success', 'flash.tribunal.createdSuccessfully');

            return $this->redirectToRoute('lucca_tribunal_show', ['id' => $tribunal->getId()]);
        }

        return $this->render('@LuccaParameter/Tribunal/new.html.twig', [
            'tribunal' => $tribunal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Tribunal entity.
     */
    #[Route(path: '/{id}', name: 'lucca_tribunal_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Tribunal $tribunal): Response
    {
        $deleteForm = $this->createDeleteForm($tribunal);

        return $this->render('@LuccaParameter/Tribunal/show.html.twig', [
            'tribunal' => $tribunal,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Tribunal entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_tribunal_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Tribunal $tribunal): Response
    {
        $deleteForm = $this->createDeleteForm($tribunal);
        $editForm = $this->createForm(TribunalType::class, $tribunal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($tribunal);
            $this->em->flush();

            $this->addFlash('info', 'flash.tribunal.updatedSuccessfully');

            return $this->redirectToRoute('lucca_tribunal_show', ['id' => $tribunal->getId()]);
        }

        return $this->render('@LuccaParameter/Tribunal/edit.html.twig', [
            'tribunal' => $tribunal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Tribunal entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_tribunal_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Tribunal $tribunal): Response
    {
        $form = $this->createDeleteForm($tribunal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($tribunal);
            $this->em->flush();
        }

        $this->addFlash('danger', 'flash.tribunal.deletedSuccessfully');

        return $this->redirectToRoute('lucca_tribunal_index');
    }

    /**
     * Creates a form to delete a Tribunal entity.
     */
    private function createDeleteForm(Tribunal $tribunal): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_tribunal_delete', ['id' => $tribunal->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Tribunal entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_tribunal_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Tribunal $tribunal): Response
    {
        if ($tribunal->isEnabled()) {
            $tribunal->setEnabled(false);
            $this->addFlash('info', 'flash.tribunal.disabledSuccessfully');
        } else {
            $tribunal->setEnabled(true);
            $this->addFlash('info', 'flash.tribunal.enabledSuccessfully');
        }

        $this->em->flush();

        return $this->redirectToRoute('lucca_tribunal_index', ['id' => $tribunal->getId()]);
    }
}
