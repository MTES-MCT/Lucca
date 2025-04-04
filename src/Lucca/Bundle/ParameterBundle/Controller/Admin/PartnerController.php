<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\ParameterBundle\Entity\Partner;
use Lucca\Bundle\ParameterBundle\Form\PartnerType;

#[Route(path: '/partner')]
#[IsGranted('ROLE_ADMIN')]
class PartnerController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of Partner
     */
    #[Route(path: '/', name: 'lucca_partner_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $partners = $this->em->getRepository(Partner::class)->findAll();

        return $this->render('@LuccaParameter/Partner/index.html.twig', [
            'partners' => $partners
        ]);
    }

    /**
     * Creates a new Partner entity.
     */
    #[Route(path: '/new', name: 'lucca_partner_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $partner = new Partner();

        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($partner);
            $this->em->flush();

            $this->addFlash('success', 'flash.partner.createdSuccessfully');
            return $this->redirectToRoute('lucca_partner_show', ['id' => $partner->getId()]);
        }

        return $this->render('@LuccaParameter/Partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Partner entity.
     */
    #[Route(path: '/{id}', name: 'lucca_partner_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Partner $partner): Response
    {
        $deleteForm = $this->createDeleteForm($partner);

        return $this->render('@LuccaParameter/Partner/show.html.twig', [
            'partner' => $partner,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Partner entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_partner_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Partner $partner): Response
    {
        $deleteForm = $this->createDeleteForm($partner);
        $editForm = $this->createForm(PartnerType::class, $partner);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($partner);
            $this->em->flush();

            $this->addFlash('info', 'flash.partner.updatedSuccessfully');

            return $this->redirectToRoute('lucca_partner_show', ['id' => $partner->getId()]);
        }

        return $this->render('@LuccaParameter/Partner/edit.html.twig', [
            'partner' => $partner,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Partner entity.
     */
    #[Route(path: '/{id}', name: 'lucca_partner_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Partner $partner): Response
    {
        $form = $this->createDeleteForm($partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($partner);
            $this->em->flush();
        }

        $this->addFlash('success', 'flash.partner.deletedSuccessfully');

        return $this->redirectToRoute('lucca_partner_index');
    }

    /**
     * Creates a form to delete a Partner entity.
     */
    private function createDeleteForm(Partner $partner): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_partner_delete', ['id' => $partner->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Partner entity.
     */
    #[Route(path: '/{id/enable}', name: 'lucca_partner_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Partner $partner): Response
    {
        if ($partner->isEnabled()) {
            $partner->setEnabled(false);
            $this->addFlash('info', 'flash.partner.disabledSuccessfully');
        } else {
            $partner->setEnabled(true);
            $this->addFlash('info', 'flash.partner.enabledSuccessfully');
        }

        $this->em->flush();

        return $this->redirectToRoute('lucca_partner_index', ['id' => $partner->getId()]);
    }
}
