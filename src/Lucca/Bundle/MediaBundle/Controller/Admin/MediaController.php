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
use Lucca\Bundle\MediaBundle\Entity\Gallery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\MediaBundle\Form\Admin\MediaType;
use Lucca\Bundle\MediaBundle\Manager\FileManager;

#[Route(path: '/media')]
#[IsGranted('ROLE_ADMIN')]
class MediaController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileManager $fileManager,
    )
    {
    }

    /**
     * Media index
     */
    #[Route(path: '/', name: 'lucca_media_index', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_READ')]
    public function indexAction(Request $request): Response
    {
        $page = intval($request->get('page'));
        if (!$page) {
            $page = 1;
        }

        $medias = $this->em->getRepository(Media::class)->findAllPaginate(
            $page, $this->getParameter('lucca_media.elements_to_load_scroll')
        );

        if ($medias) {
            $pagination = [
                'page' => $page,
                'nbPages' => ceil(count($medias) / $this->getParameter('lucca_media.elements_to_load_scroll')),
                'nomRoute' => 'front_articles_index',
                'paramsRoute' => [],
            ];

            /** If its an other page - display just an embed view */
            if ($page !== 1) {
                return $this->render(@'LuccaMedia/Media/Embed/_list-media.html.twig', [
                    'medias' => $medias,
                ]);
            }

            /** Else display the main template */
            return $this->render('@LuccaMedia/Media/Admin/index.html.twig', [
                'medias' => $medias,
                'pagination' => $pagination,
            ]);
        }

        return new Response("", Response::HTTP_NO_CONTENT);
    }

    /**
     * New media action
     */
    #[Route(path: '/new', name: 'lucca_media_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MEDIA_WRITE')]
    public function newAction(Request $request): Response
    {
        $media = new Media();

        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($media);
            $this->em->flush();

            $this->addFlash('success', 'flash.media.createdSuccessfully');

            return $this->redirectToRoute('lucca_media_index');
        }

        return $this->render('@LuccaMedia/Media/Admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Media entity.
     */
    #[Route(path: '-{id}/edit-modal', name: 'lucca_media_modal_edit', requirements: ['id' => '\d+'], options: ['expose' => true], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MEDIA_WRITE')]
    public function modalEditAction(Request $request, Media $media): Response
    {
        $editForm = $this->createForm(MediaType::class, $media);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($media);
            $this->em->flush();

            $this->addFlash('success', 'flash.media.updatedSuccessfully');

            return $this->redirectToRoute('lucca_media_index');
        }

        return $this->render('@LuccaMedia/Media/Admin/modal-edit.html.twig', [
            'media' => $media,
            'form' => $editForm->createView(),
        ]);
    }

    /**
     * Deletes a Media entity.
     */
    #[Route(path: '-{id}/delete', name: 'lucca_media_delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MEDIA_WRITE')]
    public function deleteAction(Media $media): Response
    {
        /** Find if Media is used by a Gallery */
        $gallery = $this->em->getRepository(Gallery::class)->findWithThisMedia($media);

        if (!$gallery) {
            $this->fileManager->removeFile($media);
            $this->em->remove($media);

            $this->em->flush();
            $this->addFlash('success', 'flash.media.deletedSuccessfully');
        } else {
            $this->addFlash('warning', 'flash.media.usedInGallery');
        }

        return $this->redirectToRoute('lucca_media_index');
    }

    /**
     * Finds and enable / disable a Media entity.
     */
    #[Route(path: '-{id}/enable', name: 'lucca_media_enable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route(path: '-{id}/disable', name: 'lucca_media_disable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_TOTAL')]
    public function enableAction(Request $request, Media $media): Response
    {
        if ($media->getEnabled()) {
            $media->setEnabled(false);
            $this->addFlash('success', 'flash.media.disabledSuccessfully');
        } else {
            $media->setEnabled(true);
            $this->addFlash('success', 'flash.media.enabledSuccessfully');
        }

        $this->em->flush();

        if ($request->headers->get('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('lucca_media_index');
    }
}
