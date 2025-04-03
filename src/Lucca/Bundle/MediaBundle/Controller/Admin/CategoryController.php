<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\MediaBundle\Entity\Category;
use Lucca\Bundle\MediaBundle\Form\Admin\CategoryType;

#[Route(path: '/media/category')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of category
     */
    #[Route(path: '/', name: 'lucca_media_category_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function indexAction(): Response
    {
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('@LuccaMedia/Category/Admin/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * New category action
     */
    #[Route(path: '/new', name: 'lucca_media_category_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function newAction(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($category->getMetasDatasModels() as $metaData) {
                if ($metaData->isEmpty()) {
                    $category->removeMetasDatasModel($metaData);
                }
            }

            foreach ($category->getExtensions() as $extension) {
                if ($extension->isEmpty()) {
                    $category->removeExtension($extension);
                }
            }

            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash('success', 'flash.category.createdSuccessfully');

            return $this->redirectToRoute('lucca_media_category_show', ['id' => $category->getId()]);
        }

        return $this->render('@LuccaMedia/Category/Admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Category entity.
     */
    #[Route(path: '-{id}', name: 'lucca_media_category_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showAction(Category $category): Response
    {
        $deleteForm = $this->createDeleteForm($category);

        return $this->render('@LuccaMedia/Category/Admin/show.html.twig', [
            'category' => $category,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Category entity.
     */
    #[Route(path: '-{id}/edit', name: 'lucca_media_category_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function editAction(Request $request, Category $category): Response
    {
        $editForm = $this->createForm(CategoryType::class, $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash('success', 'flash.category.updatedSuccessfully');

            return $this->redirectToRoute('lucca_media_category_index');
        }

        return $this->render('@LuccaMedia/Category/Admin/edit.html.twig', [
            'category' => $category,
            'form' => $editForm->createView(),
        ]);
    }


    /**
     * Deletes a Category entity.
     */
    #[Route(path: '-{id}', name: 'lucca_media_category_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function deleteAction(Request $request, Category $category): Response
    {
        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($category);
            $this->em->flush();

            $this->addFlash('success', 'flash.category.deletedSuccessfully');
        } else {
            $this->addFlash('success', 'flash.category.deletedCancelled');
        }

        return $this->redirectToRoute('lucca_media_category_index');
    }

    /**
     * Creates a form to delete a Category entity.
     */
    private function createDeleteForm(Category $category): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_media_category_delete', ['id' => $category->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Category entity.
     */
    #[Route(path: '-{id}/enable', name: 'lucca_media_category_enable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route(path: '-{id}/disable}', name: 'lucca_media_category_disable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function enableAction(Request $request, Category $category): Response
    {
        if ($category->getEnabled()) {
            $category->setEnabled(false);
            $this->addFlash('success', 'flash.category.disabledSuccessfully');
        } else {
            $category->setEnabled(true);
            $this->addFlash('info', 'flash.category.enabledSuccessfully');
        }

        $this->em->flush();

        if ($request->headers->get('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('lucca_media_category_index');
    }
}
