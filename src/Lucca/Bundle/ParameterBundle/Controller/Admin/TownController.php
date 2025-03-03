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

use Lucca\Bundle\ParameterBundle\Entity\Town;
use Lucca\Bundle\ParameterBundle\Form\TownType;

#[Route(path: '/town')]
#[IsGranted('ROLE_ADMIN')]
class TownController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of Town
     */
    #[Route(path: '/', name: 'lucca_town_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $towns = $this->em->getRepository(Town::class)->findAll();

        return $this->render('@LuccaParameterBundle:Town:index.html.twig', [
            'towns' => $towns
        ]);
    }

    /**
     * Creates a new Town entity.
     */
    #[Route(path: '/new', name: 'lucca_town_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $town = new Town();

        $form = $this->createForm(TownType::class, $town);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($town);
            $this->em->flush();

            $this->addFlash('success', 'flash.town.createdSuccessfully');

            return $this->redirectToRoute('lucca_town_show', ['id' => $town->getId()]);
        }

        return $this->render('@LuccaParameter/Town/new.html.twig', [
            'town' => $town,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Town entity.
     */
    #[Route(path: '/{id}', name: 'lucca_town_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Town $town): Response
    {
        $deleteForm = $this->createDeleteForm($town);

        return $this->render('@LuccaParameter/Town/show.html.twig', [
            'town' => $town,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Town entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_town_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Town $town): Response
    {
        $deleteForm = $this->createDeleteForm($town);
        $editForm = $this->createForm(TownType::class, $town);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($town);
            $this->em->flush();

            $this->addFlash('info', 'flash.town.updatedSuccessfully');

            return $this->redirectToRoute('lucca_town_show', ['id' => $town->getId()]);
        }

        return $this->render('@LuccaParameter/Town/edit.html.twig', [
            'town' => $town,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Town entity.
     */
    #[Route(path: '/{id}', name: 'lucca_town_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Town $town): Response
    {
        $form = $this->createDeleteForm($town);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($town);
            $this->em->flush();
        }

        $this->addFlash('danger', 'flash.town.deletedSuccessfully');

        return $this->redirectToRoute('lucca_town_index');
    }

    /**
     * Creates a form to delete a Town entity.
     */
    private function createDeleteForm(Town $town): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_town_delete', ['id' => $town->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Town entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_town_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Town $town): Response
    {
        if ($town->isEnabled()) {
            $town->setEnabled(false);
            $this->addFlash('info', 'flash.town.disabledSuccessfully');
        } else {
            $town->setEnabled(true);
            $this->addFlash('info', 'flash.town.enabledSuccessfully');
        }

        $this->em->flush();

        return $this->redirectToRoute('lucca_town_index', ['id' => $town->getId()]);
    }
}
