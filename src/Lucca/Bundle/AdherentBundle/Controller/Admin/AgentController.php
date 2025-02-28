<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

use Lucca\Bundle\AdherentBundle\Entity\{Agent, Adherent};
use Lucca\Bundle\AdherentBundle\Form\AgentType;

#[Route(path: '/adherent-{adh_id}/agent')]
#[IsGranted('ROLE_ADMIN')]
class AgentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * Creates a new Agent entity.
     */
    #[Route(path: '/new', name: 'lucca_agent_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(
        #[MapEntity(id: 'adh_id')] Adherent $adherent,
        Request                             $request,
    ): Response
    {
        $agent = new Agent();

        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adherent->addAgent($agent);

            $this->em->persist($adherent);
            $this->em->persist($agent);
            $this->em->flush();

            $this->addFlash('success', 'flash.agent.createdSuccessfully');
            return $this->redirectToRoute('lucca_adherent_show', ['id' => $adherent->getId()]);
        }

        return $this->render('@LuccaAdherent/Agent/new.html.twig', [
            'agent' => $agent,
            'adherent' => $adherent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Agent entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_agent_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(
        #[MapEntity(id: 'adh_id')] Adherent $adherent,
        Agent                               $agent,
        Request                             $request,
    ): Response
    {
        $deleteForm = $this->createDeleteForm($agent);
        $editForm = $this->createForm(AgentType::class, $agent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($agent);
            $this->em->flush();

            $this->addFlash('info', 'flash.agent.updatedSuccessfully');

            return $this->redirectToRoute('lucca_adherent_show', ['id' => $adherent->getId()]);
        }

        return $this->render('@LuccaAdherent/Agent/edit.html.twig', [
            'agent' => $agent,
            'adherent' => $adherent,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Agent entity.
     */
    #[Route(path: '/{id}', name: 'lucca_agent_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(
        #[MapEntity(id: 'adh_id')] Adherent $adherent,
        Agent                               $agent,
        Request                             $request,
    ): Response
    {
        $form = $this->createDeleteForm($agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($agent);
            $this->em->flush();
        }

        $this->addFlash('info', 'flash.agent.deletedSuccessfully');

        return $this->redirectToRoute('lucca_adherent_show', ['id' => $adherent->getId()]);
    }

    /**
     * Creates a form to delete a Agent entity.
     */
    private function createDeleteForm(Agent $agent): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_agent_delete', ['adh_id' => $agent->getAdherent()->getId(), 'id' => $agent->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Agent entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_agent_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(
        #[MapEntity(id: 'adh_id')] Adherent $adherent,
        Agent                               $agent,
    ): Response
    {
        if ($agent->isEnabled()) {
            $agent->setEnabled(false);
            $this->addFlash('info', 'flash.agent.enabledSuccessfully');
        } else {
            $agent->setEnabled(true);
            $this->addFlash('info', 'flash.agent.disabledSuccessfully');
        }

        $this->em->flush();

        return $this->redirectToRoute('lucca_adherent_show', ['id' => $adherent->getId()]);
    }
}
