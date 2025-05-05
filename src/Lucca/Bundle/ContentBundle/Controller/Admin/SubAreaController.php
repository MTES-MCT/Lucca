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

use Lucca\Bundle\ContentBundle\Entity\{Page, SubArea};
use Lucca\Bundle\ContentBundle\Form\SubAreaType;
use Lucca\Bundle\CoreBundle\Utils\Canonalizer;

#[Route(path: '/subarea')]
#[IsGranted('ROLE_ADMIN')]
class SubAreaController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Canonalizer            $canonalizer,
    )
    {
    }

    /**
     * List of SubArea
     */
    #[Route(path: '/', name: 'lucca_subarea_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $subAreas = $this->em->getRepository(SubArea::class)->findAll();

        return $this->render('@LuccaContent/SubArea/index.html.twig', [
            'subAreas' => $subAreas
        ]);
    }

    /**
     * Creates a new SubArea entity.
     */
    #[Route(path: '/new', name: 'lucca_subarea_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $subArea = new SubArea();
        $subArea->setWidth('col-lg-4 col-md-6 col-sm-12 col-xs12');

        $form = $this->createForm(SubAreaType::class, $subArea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($subArea);

            /** Set the code after persist to get an ID */
            $subArea->setCode($this->canonalizer->slugify($subArea->getName() . '-' . $subArea->getId()));

            $this->em->flush();

            $this->addFlash('success', 'flash.created_successfully');

            return $this->redirectToRoute('lucca_subarea_show', ['id' => $subArea->getId()]);
        }

        return $this->render('@LuccaContent/SubArea/new.html.twig', [
            'subArea' => $subArea,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a SubArea entity.
     */
    #[Route(path: '/{id}', name: 'lucca_subarea_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(SubArea $subArea): Response
    {
        $deleteForm = $this->createDeleteForm($subArea);

        $pages = $this->em->getRepository(Page::class)->findBy([
            'subarea' => $subArea
        ]);

        return $this->render('@LuccaContent/SubArea/show.html.twig', [
            'subArea' => $subArea,
            'pages' => $pages,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing SubArea entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_subarea_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, SubArea $subArea): Response
    {
        $deleteForm = $this->createDeleteForm($subArea);
        $editForm = $this->createForm(SubAreaType::class, $subArea);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $subArea->setCode($this->canonalizer->slugify($subArea->getName() . '-' . $subArea->getId()));

            $this->em->persist($subArea);
            $this->em->flush();

            $this->addFlash('success', 'flash.updated_successfully');

            return $this->redirectToRoute('lucca_subarea_show', ['id' => $subArea->getId()]);
        }

        return $this->render('@LuccaContent/SubArea/edit.html.twig', [
            'subArea' => $subArea,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a SubArea entity.
     */
    #[Route(path: '/{id}', name: 'lucca_subarea_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, SubArea $subArea): Response
    {
        $form = $this->createDeleteForm($subArea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($subArea);
            $this->em->flush();
        }

        $this->addFlash('success', 'flash.deleted_successfully');

        return $this->redirectToRoute('lucca_subarea_index');
    }

    /**
     * Creates a form to delete a SubArea entity.
     */
    private function createDeleteForm(SubArea $subArea): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_subarea_delete', ['id' => $subArea->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a SubArea entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_subarea_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(SubArea $subArea): Response
    {
        $subArea->toggle();

        $this->em->flush();

        $this->addFlash('success', 'flash.toggled_successfully');

        return $this->redirectToRoute('lucca_subarea_index');
    }
}
