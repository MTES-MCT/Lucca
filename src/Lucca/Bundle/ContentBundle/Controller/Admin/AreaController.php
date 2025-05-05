<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\ContentBundle\Entity\{SubArea, Area};
use Lucca\Bundle\ContentBundle\Form\AreaType;

#[Route(path: '/area')]
#[IsGranted('ROLE_ADMIN')]
class AreaController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of Area
     */
    #[Route(path: '/', name: 'lucca_area_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $areas = $this->em->getRepository(Area::class)->findAll();

        return $this->render('@LuccaContent/Area/index.html.twig', [
            'areas' => $areas
        ]);
    }

    /**
     * Creates a new Area entity.
     */
    #[Route(path: '/new', name: 'lucca_area_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $area = new Area();

        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($area);
            $this->em->flush();

            $this->addFlash('success', 'flash.created_successfully');

            return $this->redirectToRoute('lucca_area_show', ['id' => $area->getId()]);
        }

        return $this->render('@LuccaContent/Area/new.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Area entity.
     */
    #[Route(path: '/{id}', name: 'lucca_area_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Area $area): Response
    {
        $deleteForm = $this->createDeleteForm($area);

        $subareas = $this->em->getRepository(SubArea::class)->findBy([
            'area' => $area
        ]);

        return $this->render('@LuccaContent/Area/show.html.twig', [
            'area' => $area,
            'subareas' => $subareas,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Area entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_area_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Area $area): Response
    {
        $deleteForm = $this->createDeleteForm($area);
        $editForm = $this->createForm(AreaType::class, $area);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($area);
            $this->em->flush();

            $this->addFlash('success', 'flash.updated_successfully');

            return $this->redirectToRoute('lucca_area_show', ['id' => $area->getId()]);
        }

        return $this->render('@LuccaContent/Area/edit.html.twig', [
            'area' => $area,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Area entity.
     */
    #[Route(path: '/{id}', name: 'lucca_area_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Area $area): Response
    {
        $form = $this->createDeleteForm($area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($area);
            $this->em->flush();
        }

        $this->addFlash('success', 'flash.deleted_successfully');

        return $this->redirectToRoute('lucca_area_index');
    }

    /**
     * Creates a form to delete a Area entity.
     */
    private function createDeleteForm(Area $area): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_area_delete', ['id' => $area->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Area entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_area_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Area $area): Response
    {
        $area->toggle();
        $this->em->flush();

        $this->addFlash('success', 'flash.toggled_successfully');

        return $this->redirectToRoute('lucca_area_index');
    }
}
