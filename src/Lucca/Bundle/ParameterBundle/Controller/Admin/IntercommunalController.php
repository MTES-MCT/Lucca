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

use Lucca\Bundle\ParameterBundle\Entity\{Town, Intercommunal};
use Lucca\Bundle\ParameterBundle\Form\IntercommunalType;

#[Route(path: '/intercommunal')]
#[IsGranted('ROLE_ADMIN')]
class IntercommunalController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of Intercommunal
     */
    #[Route(path: '/', name: 'lucca_intercommunal_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $intercommunals = $this->em->getRepository(Intercommunal::class)->findAll();

        return $this->render('@LuccaParameter/Intercommunal/index.html.twig', [
            'intercommunals' => $intercommunals
        ]);
    }

    /**
     * Creates a new Intercommunal entity.
     */
    #[Route(path: '/new', name: 'lucca_intercommunal_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $intercommunal = new Intercommunal();

        $form = $this->createForm(IntercommunalType::class, $intercommunal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($intercommunal);
            $this->em->flush();

            $this->addFlash('success', 'flash.intercommunal.createdSuccessfully');

            return $this->redirectToRoute('lucca_intercommunal_show', ['id' => $intercommunal->getId()]);
        }

        return $this->render('@LuccaParameter/Intercommunal/new.html.twig', [
            'intercommunal' => $intercommunal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Intercommunal entity.
     */
    #[Route(path: '/{id}', name: 'lucca_intercommunal_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Intercommunal $intercommunal): Response
    {
        $deleteForm = $this->createDeleteForm($intercommunal);

        $towns = $this->em->getRepository(Town::class)->findBy([
            'intercommunal' => $intercommunal
        ]);

        return $this->render('@LuccaParameter/Intercommunal/show.html.twig', [
            'intercommunal' => $intercommunal,
            'towns' => $towns,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Intercommunal entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_intercommunal_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Intercommunal $intercommunal): Response
    {
        $deleteForm = $this->createDeleteForm($intercommunal);
        $editForm = $this->createForm(IntercommunalType::class, $intercommunal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($intercommunal);
            $this->em->flush();

            $this->addFlash('success', 'flash.intercommunal.updatedSuccessfully');

            return $this->redirectToRoute('lucca_intercommunal_show', ['id' => $intercommunal->getId()]);
        }

        return $this->render('@LuccaParameter/Intercommunal/edit.html.twig', [
            'intercommunal' => $intercommunal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Intercommunal entity.
     */
    #[Route(path: '/{id}', name: 'lucca_intercommunal_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Intercommunal $intercommunal): Response
    {
        $form = $this->createDeleteForm($intercommunal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($intercommunal);
            $this->em->flush();
        }

        $this->addFlash('success', 'flash.intercommunal.deletedSuccessfully');

        return $this->redirectToRoute('lucca_intercommunal_index');
    }

    /**
     * Creates a form to delete a Intercommunal entity.
     */
    private function createDeleteForm(Intercommunal $intercommunal): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_intercommunal_delete', ['id' => $intercommunal->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Intercommunal entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_intercommunal_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Intercommunal $intercommunal): Response
    {
        if ($intercommunal->isEnabled()) {
            $intercommunal->setEnabled(false);
            $this->addFlash('info', 'flash.intercommunal.disabledSuccessfully');
        } else {
            $intercommunal->setEnabled(true);
            $this->addFlash('info', 'flash.intercommunal.enabledSuccessfully');
        }

        $this->em->flush();

        return $this->redirectToRoute('lucca_intercommunal_index', ['id' => $intercommunal->getId()]);
    }
}
