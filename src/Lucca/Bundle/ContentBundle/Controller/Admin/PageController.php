<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

use Lucca\Bundle\ContentBundle\Entity\Page;
use Lucca\Bundle\ContentBundle\Form\PageType;
use Lucca\Bundle\ContentBundle\Manager\PageManager;

#[Route(path: '/page')]
#[IsGranted('ROLE_ADMIN')]
class PageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PageManager            $pageManager,
    )
    {
    }

    /**
     * List of Page
     */
    #[Route(path: '/', name: 'lucca_page_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $pages = $this->em->getRepository(Page::class)->findAll();

        return $this->render('@LuccaContent/Page/index.html.twig', [
            'pages' => $pages
        ]);
    }

    /**
     * Creates a new Page entity.
     */
    #[Route(path: '/new', name: 'lucca_page_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $page = new Page();

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page = $this->pageManager->managePageAndMediasLinked($page);

            $this->em->persist($page);
            $this->em->flush();

            $this->addFlash('success', 'flashes.created_successfully');

            return $this->redirectToRoute('lucca_page_show', ['id' => $page->getId()]);
        }

        return $this->render('@LuccaContent/Page/new.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Page entity.
     */
    #[Route(path: '/{id}', name: 'lucca_page_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Page $page): Response
    {
        $deleteForm = $this->createDeleteForm($page);

        return $this->render('@LuccaContent/Page/show.html.twig', [
            'page' => $page,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Page entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_page_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Page $page): Response
    {
        $editForm = $this->createForm(PageType::class, $page);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $page = $this->pageManager->managePageAndMediasLinked($page);

            $this->em->persist($page);
            $this->em->flush();

            $this->addFlash('success', 'flashes.updated_successfully');

            return $this->redirectToRoute('lucca_page_show', ['id' => $page->getId()]);
        }

        return $this->render('@LuccaContent/Page/edit.html.twig', [
            'page' => $page,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Deletes a Page entity.
     */
    #[Route(path: '/{id}', name: 'lucca_page_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Page $page): Response
    {
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($page);
            $this->em->flush();
        }

        $this->addFlash('success', 'flashes.deleted_successfully');

        return $this->redirectToRoute('lucca_page_index');
    }

    /**
     * Creates a form to delete a Page entity.
     */
    private function createDeleteForm(Page $page): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_page_delete', ['id' => $page->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Page entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_page_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Page $page): Response
    {
        $page->toggle();

        $this->em->flush();

        $this->addFlash('success', 'flashes.toggled_successfully');

        return $this->redirectToRoute('lucca_page_index');
    }
}
