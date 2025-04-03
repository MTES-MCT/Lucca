<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Tag;
use Lucca\Bundle\FolderBundle\Form\TagType;

#[IsGranted('ROLE_ADMIN')]
#[Route(path: '/tag')]
class TagController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of Tag
     */
    #[Route(path: '/', name: 'lucca_tag_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $tags = $this->em->getRepository(Tag::class)->findAll();

        return $this->render('@LuccaFolder/Tag/index.html.twig', [
            'tags' => $tags
        ]);
    }

    /**
     * Creates a new Tag entity.
     */
    #[Route(path: '/new', name: 'lucca_tag_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($tag->getProposals() as $proposal) {
                $tag->addProposal($proposal);
            }

            $this->em->persist($tag);
            $this->em->flush();

            $this->addFlash('success', 'flash.tag.createdSuccessfully');

            return $this->redirectToRoute('lucca_tag_show', ['id' => $tag->getId()]);
        }

        return $this->render('@LuccaFolder/Tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Tag entity.
     */
    #[Route(path: '/{id}', name: 'lucca_tag_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Tag $tag): Response
    {
        $deleteForm = $this->createDeleteForm($tag);

        return $this->render('@LuccaFolder/Tag/show.html.twig', [
            'tag' => $tag,
            'proposals' => $tag->getProposals(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Tag entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_tag_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Tag $tag): Response
    {
        $deleteForm = $this->createDeleteForm($tag);
        $editForm = $this->createForm(TagType::class, $tag);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($tag->getProposals() as $proposal) {
                $tag->addProposal($proposal);
            }

            $this->em->persist($tag);
            $this->em->flush();

            $this->addFlash('success', 'flash.tag.updatedSuccessfully');

            return $this->redirectToRoute('lucca_tag_show', ['id' => $tag->getId()]);
        }

        return $this->render('@LuccaFolder/Tag/edit.html.twig', [
            'tag' => $tag,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Tag entity.
     */
    #[Route(path: '/{id}', name: 'lucca_tag_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Tag $tag): Response
    {
        $form = $this->createDeleteForm($tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($tag);
            $this->em->flush();
        }

        $this->addFlash('success', 'flash.tag.deletedSuccessfully');

        return $this->redirectToRoute('lucca_tag_index');
    }

    /**
     * Creates a form to delete a Tag entity.
     */
    private function createDeleteForm(Tag $tag): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_tag_delete', ['id' => $tag->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Tag entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_tag_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Tag $tag): Response
    {
        if ($tag->getEnabled()) {
            $tag->setEnabled(false);
            $this->addFlash('success', 'flash.tag.disabledSuccessfully');
        } else {
            $tag->setEnabled(true);
            $this->addFlash('success', 'flash.tag.enabledSuccessfully');
        }

        $this->em->flush();

        return $this->redirectToRoute('lucca_tag_index');
    }
}
