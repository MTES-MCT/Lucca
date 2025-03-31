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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\MediaBundle\Entity\Storager;
use Lucca\Bundle\MediaBundle\Form\Admin\StoragerType;

#[Route(path: '/media/storager')]
#[IsGranted('ROLE_ADMIN')]
class StoragerController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of storager
     */
    #[Route(path: '/', name: 'lucca_media_storager_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function indexAction(): Response
    {
        $storagers = $this->em->getRepository(Storager::class)->findAll();

        return $this->render('@LuccaMedia/Storager/Admin/index.html.twig', [
            'storagers' => $storagers
        ]);
    }

    /**
     * New storager action
     */
    #[Route(path: '/new', name: 'lucca_media_storager_new', defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function newAction(Request $request): Response
    {
        $storager = new Storager();

        $form = $this->createForm(StoragerType::class, $storager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($storager);
            $this->em->flush();

            $this->addFlash('success', 'flash.storager.createdSuccessfully');

            return $this->redirectToRoute('lucca_media_storager_show', ['id' => $storager->getId()]);
        }

        return $this->render('@LuccaMedia/Storager/Admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Storager entity.
     */
    #[Route(path: '-{id}', name: 'lucca_media_storager_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showAction(Storager $storager): Response
    {
        $deleteForm = $this->createDeleteForm($storager);

        return $this->render('@LuccaMedia/Storager/Admin/show.html.twig', [
            'storager' => $storager,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Storager entity.
     */
    #[Route(path: '-{id}/edit', name: 'lucca_media_storager_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function editAction(Request $request, Storager $storager): Response
    {
        $editForm = $this->createForm(StoragerType::class, $storager);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($storager);
            $this->em->flush();

            $this->addFlash('success', 'flash.storager.updatedSuccessfully');

            return $this->redirectToRoute('lucca_media_storager_index');
        }

        return $this->render('@LuccaMedia/Storager/Admin/edit.html.twig', [
            'storager' => $storager,
            'edit_form' => $editForm->createView(),
        ]);
    }


    /**
     * Deletes a Storager entity.
     */
    #[Route(path: '-{id}', name: 'lucca_media_storager_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteAction(Request $request, Storager $storager): Response
    {
        $form = $this->createDeleteForm($storager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($storager);
            $this->em->flush();
            $this->addFlash('success', 'flash.storager.deletedSuccessfully');
        } else {
            $this->addFlash('warning', 'flash.storager.deletedCancelled');
        }

        return $this->redirectToRoute('lucca_media_storager_index');
    }

    /**
     * Creates a form to delete a Storager entity.
     */
    private function createDeleteForm(Storager $storager): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_media_storager_delete', ['id' => $storager->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Storager entity.
     */
    #[Route(path: '-{id}/enable', name: 'lucca_media_storager_enable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route(path: '-{id}/disable', name: 'lucca_media_storager_disable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function enableAction(Request $request, Storager $storager): Response
    {
        if ($storager->getEnabled()) {
            $storager->setEnabled(false);
            $this->addFlash('success', 'flash.storager.disabledSuccessfully');
        } else {
            $storager->setEnabled(true);
            $this->addFlash('success', 'flash.storager.enabledSuccessfully');
        }

        $this->em->flush();

        if ($request->headers->get('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('lucca_media_storager_index');
    }
}
