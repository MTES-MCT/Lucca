<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\MediaBundle\Entity\Gallery;
use Lucca\Bundle\MediaBundle\Form\Admin\GalleryType;

#[Route(path: '/media/gallery')]
#[IsGranted('ROLE_ADMIN')]
class GalleryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * Gallery index
     */
    #[Route(path: '/', name: 'lucca_media_gallery_index', methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_READ')]
    public function indexAction(): Response
    {
        $galleries = $this->em->getRepository(Gallery::class)->FindAll();

        return $this->render('@LuccaMedia/Gallery/Admin/index.html.twig', [
            'galleries' => $galleries
        ]);
    }

    /**
     * New gallery action
     */
    #[Route(path: '/new', name: 'lucca_media_gallery_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MEDIA_WRITE')]
    public function newAction(Request $request): Response
    {
        $gallery = new Gallery();

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($gallery);
            $this->em->flush();

            $this->addFlash('success', 'flash.gallery.createdSuccessfully');

            return $this->redirectToRoute('lucca_media_gallery_show', ['id' => $gallery->getId()]);
        }

        return $this->render('@LuccaMedia/Gallery/Admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Gallery Show
     */
    #[Route(path: '-{id}', name: 'lucca_media_gallery_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_READ')]
    public function showAction(Gallery $gallery): Response
    {
        $deleteForm = $this->createDeleteForm($gallery);

        return $this->render('@LuccaMedia/Gallery/Admin/show.html.twig', [
            'gallery' => $gallery,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Gallery entity.
     */
    #[Route(path: '-{id}/edit', name: 'lucca_media_gallery_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MEDIA_WRITE')]
    public function editAction(Request $request, Gallery $gallery): Response
    {
        $editForm = $this->createForm(GalleryType::class, $gallery);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($gallery);
            $this->em->flush();

            $this->addFlash('success', 'flash.gallery.updatedSuccessfully');

            return $this->redirectToRoute('lucca_media_gallery_show', ['id' => $gallery->getId()]);
        }

        return $this->render('@LuccaMedia/Gallery/Admin/edit.html.twig', [
            'gallery' => $gallery,
            'form' => $editForm->createView(),
        ]);
    }

    /**
     * Deletes a Gallery entity.
     */
    #[Route(path: '-{id}', name: 'lucca_media_gallery_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('ROLE_MEDIA_TOTAL')]
    public function deleteAction(Request $request, Gallery $gallery): Response
    {
        $form = $this->createDeleteForm($gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($gallery);
            $this->em->flush();

            $this->addFlash('success', 'flash.gallery.deletedSuccessfully');
        } else {
            $this->addFlash('danger', 'flash.gallery.deletedCancelled');
        }

        return $this->redirectToRoute('lucca_media_gallery_index');
    }

    /**
     * Creates a form to delete a Gallery entity.
     */
    private function createDeleteForm(Gallery $gallery): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_media_gallery_delete', ['id' => $gallery->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Gallery entity.
     */
    #[Route(path: '-{id}/enable', name: 'lucca_media_gallery_enable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route(path: '-{id/disable}', name: 'lucca_media_gallery_disable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_TOTAL')]
    public function enableAction(Request $request, Gallery $gallery): Response
    {
        if ($gallery->getEnabled()) {
            $gallery->setEnabled(false);
            $this->addFlash('info', 'flash.gallery.disabledSuccessfully');
        } else {
            $gallery->setEnabled(true);
            $this->addFlash('info', 'flash.gallery.enabledSuccessfully');
        }

        $this->em->flush();

        if ($request->headers->get('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('lucca_media_gallery_index');
    }
}
