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

use Lucca\Bundle\FolderBundle\Entity\Natinf;
use Lucca\Bundle\FolderBundle\Form\NatinfType;

#[IsGranted('ROLE_USER')]
#[Route(path: '/natinf')]
class NatinfController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {

    }

    /**
     * List of Natinf
     */
    #[IsGranted('ROLE_USER')]
    #[Route(path: '/', name: 'lucca_natinf_index', methods: ['GET'])]
    public function indexAction(): Response
    {
        $natinfs = $this->em->getRepository(Natinf::class)->findAll();

        return $this->render('@LuccaFolder/Natinf/index.html.twig', [
            'natinfs' => $natinfs
        ]);
    }

    /**
     * Creates a new Natinf entity.
     */
    #[Route(path: '/new', name: 'lucca_natinf_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $natinf = new Natinf();

        $form = $this->createForm(NatinfType::class, $natinf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($natinf);
            $this->em->flush();

            $this->addFlash('success', 'flash.natinf.createdSuccessfully');

            return $this->redirectToRoute('lucca_natinf_show', ['id' => $natinf->getId()]);
        }

        return $this->render('@LuccaFolder/Natinf/new.html.twig', [
            'natinf' => $natinf,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Natinf entity.
     */
    #[Route(path: '/{id}', name: 'lucca_natinf_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Natinf $natinf): Response
    {
        $deleteForm = $this->createDeleteForm($natinf);

        return $this->render('@LuccaFolder/Natinf/show.html.twig', [
            'natinf' => $natinf,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Natinf entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_natinf_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Natinf $natinf): Response
    {
        $deleteForm = $this->createDeleteForm($natinf);
        $editForm = $this->createForm(NatinfType::class, $natinf);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($natinf);
            $this->em->flush();

            $this->addFlash('success', 'flash.natinf.updatedSuccessfully');

            return $this->redirectToRoute('lucca_natinf_show', ['id' => $natinf->getId()]);
        }

        return $this->render('@LuccaFolder/Natinf/edit.html.twig', [
            'natinf' => $natinf,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Natinf entity.
     */
    #[Route(path: '/{id}', name: 'lucca_natinf_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Natinf $natinf): Response
    {
        $form = $this->createDeleteForm($natinf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($natinf);
            $this->em->flush();
        }

        $this->addFlash('success', 'flash.natinf.deletedSuccessfully');

        return $this->redirectToRoute('lucca_natinf_index');
    }

    /**
     * Creates a form to delete a Natinf entity.
     */
    private function createDeleteForm(Natinf $natinf): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_natinf_delete', ['id' => $natinf->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Natinf entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_natinf_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Natinf $natinf): Response
    {
        if ($natinf->isEnabled()) {
            $natinf->setEnabled(false);
            $this->addFlash('success', 'flash.natinf.enabledSuccessfully');
        } else {
            $natinf->setEnabled(true);
            $this->addFlash('success', 'flash.natinf.disabledSuccessfully');
        }

        $this->em->flush();

        return $this->redirectToRoute('lucca_natinf_index');
    }
}
