<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Lucca\Bundle\FolderBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class TagController
 *
 * @package Lucca\Bundle\FolderBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/tag')]
class TagController extends AbstractController
{
    /**
     * List of Tag
     *
     * @return Response
     */
    #[Route('/', name: 'lucca_tag_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $tags = $em->getRepository('LuccaFolderBundle:Tag')->findAll();

        return $this->render('@LuccaFolder/Tag/index.html.twig', array(
            'tags' => $tags
        ));
    }

    /**
     * Creates a new Tag entity.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/new', name: 'lucca_tag_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): RedirectResponse|Response
    {
        $tag = new Tag();

        $form = $this->createForm('Lucca\FolderBundle\Form\TagType', $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($tag->getProposals() as $proposal) {
                $tag->addProposal($proposal);
            }

            $em->persist($tag);
            $em->flush();

            $this->addFlash('success', 'flash.tag.createdSuccessfully');
            return $this->redirectToRoute('lucca_tag_show', array('id' => $tag->getId()));
        }

        return $this->render('@LuccaFolder/Tag/new.html.twig', array(
            'tag' => $tag,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tag entity.
     *
     * @param Tag $tag
     * @return Response
     */
    #[Route('/{id}', name: 'lucca_tag_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Tag $tag): Response
    {
        $deleteForm = $this->createDeleteForm($tag);

        return $this->render('@LuccaFolder/Tag/show.html.twig', array(
            'tag' => $tag,
            'proposals' => $tag->getProposals(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tag entity.
     *
     * @param Request $request
     * @param Tag $tag
     * @return RedirectResponse|Response
     */
    #[Route('/{id}/edit', name: 'lucca_tag_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Tag $tag): RedirectResponse|Response
    {
        $deleteForm = $this->createDeleteForm($tag);
        $editForm = $this->createForm('Lucca\FolderBundle\Form\TagType', $tag);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($tag->getProposals() as $proposal) {
                $tag->addProposal($proposal);
            }

            $em->persist($tag);
            $em->flush();

            $this->addFlash('info', 'flash.tag.updatedSuccessfully');
            return $this->redirectToRoute('lucca_tag_show', array('id' => $tag->getId()));
        }

        return $this->render('@LuccaFolder/Tag/edit.html.twig', array(
            'tag' => $tag,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Tag entity.
     *
     * @param Request $request
     * @param Tag $tag
     * @return RedirectResponse
     */
    #[Route('/{id}', name: 'lucca_tag_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Tag $tag): RedirectResponse
    {
        $form = $this->createDeleteForm($tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($tag);
            $em->flush();
        }

        $this->addFlash('danger', 'flash.tag.deletedSuccessfully');
        return $this->redirectToRoute('lucca_tag_index');
    }

    /**
     * Creates a form to delete a Tag entity.
     *
     * @param Tag $tag
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Tag $tag)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_tag_delete', array('id' => $tag->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Tag entity.
     *
     * @param Tag $tag
     * @return RedirectResponse
     */
    #[Route('/{id}/enable', name: 'lucca_tag_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Tag $tag): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        if ($tag->getEnabled()) {
            $tag->setEnabled(false);
            $this->addFlash('info', 'flash.tag.disabledSuccessfully');
        } else {
            $tag->setEnabled(true);
            $this->addFlash('info', 'flash.tag.enabledSuccessfully');
        }
        $em->flush();

        return $this->redirectToRoute('lucca_tag_index');
    }
}
