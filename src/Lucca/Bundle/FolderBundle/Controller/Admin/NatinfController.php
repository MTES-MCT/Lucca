<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Lucca\Bundle\FolderBundle\Entity\Natinf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class NatinfController
 *
 * @package Lucca\Bundle\FolderBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
#[IsGranted('ROLE_USER')]
#[Route('/natinf')]
class NatinfController extends AbstractController
{
    /**
     * List of Natinf
     *
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/natinf')]
    public function indexAction(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $natinfs = $em->getRepository('LuccaFolderBundle:Natinf')->findAll();

        return $this->render('@LuccaFolder/Natinf/index.html.twig', array(
            'natinfs' => $natinfs
        ));
    }

    /**
     * Creates a new Natinf entity.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/new', name: 'lucca_natinf_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): RedirectResponse|Response
    {
        $natinf = new Natinf();

        $form = $this->createForm('Lucca\FolderBundle\Form\NatinfType', $natinf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($natinf);
            $em->flush();

            $this->addFlash('success', 'flash.natinf.createdSuccessfully');
            return $this->redirectToRoute('lucca_natinf_show', array('id' => $natinf->getId()));
        }

        return $this->render('@LuccaFolder/Natinf/new.html.twig', array(
            'natinf' => $natinf,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Natinf entity.
     *
     * @param Natinf $natinf
     * @return Response
     */
    #[Route('/{id}', name: 'lucca_natinf_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Natinf $natinf): Response
    {
        $deleteForm = $this->createDeleteForm($natinf);

        return $this->render('@LuccaFolder/Natinf/show.html.twig', array(
            'natinf' => $natinf,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Natinf entity.
     *
     * @param Request $request
     * @param Natinf $natinf
     * @return RedirectResponse|Response
     */
    #[Route('/{id}/edit', name: 'lucca_natinf_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Natinf $natinf): RedirectResponse|Response
    {
        $deleteForm = $this->createDeleteForm($natinf);
        $editForm = $this->createForm('Lucca\FolderBundle\Form\NatinfType', $natinf);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($natinf);
            $em->flush();

            $this->addFlash('info', 'flash.natinf.updatedSuccessfully');
            return $this->redirectToRoute('lucca_natinf_show', array('id' => $natinf->getId()));
        }

        return $this->render('@LuccaFolder/Natinf/edit.html.twig', array(
            'natinf' => $natinf,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Natinf entity.
     *
     * @param Request $request
     * @param Natinf $natinf
     * @return RedirectResponse
     */
    #[Route('/{id}', name: 'lucca_natinf_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Natinf $natinf): RedirectResponse
    {
        $form = $this->createDeleteForm($natinf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($natinf);
            $em->flush();
        }

        $this->addFlash('danger', 'flash.natinf.deletedSuccessfully');
        return $this->redirectToRoute('lucca_natinf_index');
    }

    /**
     * Creates a form to delete a Natinf entity.
     *
     * @param Natinf $natinf
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Natinf $natinf)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_natinf_delete', array('id' => $natinf->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Natinf entity.
     *
     * @param Natinf $natinf
     * @return RedirectResponse
     */
    #[Route('/{id}/enable', name: 'lucca_natinf_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Natinf $natinf): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        if ($natinf->isEnabled()) {
            $natinf->setEnabled(false);
            $this->addFlash('info', 'flash.natinf.enabledSuccessfully');
        } else {
            $natinf->setEnabled(true);
            $this->addFlash('info', 'flash.natinf.disabledSuccessfully');
        }

        $em->flush();

        return $this->redirectToRoute('lucca_natinf_index');
    }
}
