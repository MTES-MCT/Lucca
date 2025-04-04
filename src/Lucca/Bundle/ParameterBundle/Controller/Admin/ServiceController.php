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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\ParameterBundle\Entity\Service;
use Lucca\Bundle\ParameterBundle\Form\ServiceType;

#[Route(path: '/service')]
#[IsGranted('ROLE_ADMIN')]
class ServiceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * List of Service
     */
    #[Route(path: '/', name: 'lucca_service_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $services = $this->em->getRepository(Service::class)->findAll();

        return $this->render('@LuccaParameter/Service/index.html.twig', [
            'services' => $services
        ]);
    }

    /**
     * Creates a new Service entity.
     */
    #[Route(path: '/new', name: 'lucca_service_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $service = new Service();

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($service);
            $this->em->flush();

            $this->addFlash('success', 'flash.service.createdSuccessfully');

            return $this->redirectToRoute('lucca_service_show', ['id' => $service->getId()]);
        }

        return $this->render('@LuccaParameter/Service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Service entity.
     */
    #[Route(path: '/{id}', name: 'lucca_service_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Service $service): Response
    {
        $deleteForm = $this->createDeleteForm($service);

        return $this->render('@LuccaParameter/Service/show.html.twig', [
            'service' => $service,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Service entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_service_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Service $service): Response
    {
        $deleteForm = $this->createDeleteForm($service);
        $editForm = $this->createForm(ServiceType::class, $service);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($service);
            $this->em->flush();

            $this->addFlash('success', 'flash.service.updatedSuccessfully');

            return $this->redirectToRoute('lucca_service_show', ['id' => $service->getId()]);
        }

        return $this->render('@LuccaParameter/Service/edit.html.twig', [
            'service' => $service,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Service entity.
     */
    #[Route(path: '/{id}', name: 'lucca_service_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Service $service): Response
    {
        $form = $this->createDeleteForm($service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($service);
            $this->em->flush();
        }

        $this->addFlash('success', 'flash.service.deletedSuccessfully');

        return $this->redirectToRoute('lucca_service_index');
    }

    /**
     * Creates a form to delete a Service entity.
     */
    private function createDeleteForm(Service $service): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_service_delete', ['id' => $service->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Service entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_service_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Service $service): Response
    {
        if ($service->isEnabled()) {
            $service->setEnabled(false);
            $this->addFlash('success', 'flash.service.disabledSuccessfully');
        } else {
            $service->setEnabled(true);
            $this->addFlash('success', 'flash.service.enabledSuccessfully');
        }

        $this->em->flush();

        return $this->redirectToRoute('lucca_service_index', ['id' => $service->getId()]);
    }
}
