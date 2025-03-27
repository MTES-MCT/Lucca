<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Controller\MyAgent;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

use Lucca\Bundle\AdherentBundle\Entity\{Adherent, Agent};
use Lucca\Bundle\AdherentBundle\Form\AgentType;

#[Route(path: '/my-agent')]
#[IsGranted('ROLE_VISU')]
class MyAgentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TokenStorageInterface $tokenStorage,
    )
    {
    }

    /**
     * List of Agent
     */
    #[Route(path: '/', name: 'lucca_myagent_index', methods: ['GET'])]
    #[IsGranted('ROLE_VISU')]
    public function indexAction(): Response
    {
        /** Find Adherent by connected User */
        $user = $this->tokenStorage->getToken()->getUser();
        $adherent = $this->em->getRepository(Adherent::class)->findOneBy([
            'user' => $user
        ]);

        $agents = $this->em->getRepository(Agent::class)->findAllByAdherent($adherent);

        return $this->render('@LuccaAdherent/MyAgent/index.html.twig', [
            'user' => $user,
            'agents' => $agents
        ]);
    }

    /**
     * Creates a new Agent entity.
     */
    #[Route(path: '/new', name: 'lucca_myagent_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(Request $request): Response
    {
        /** Find Adherent by connected User */
        $user = $this->tokenStorage->getToken()->getUser();
        $adherent = $this->em->getRepository(Adherent::class)->findOneBy([
            'user' => $user
        ]);

        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adherent->addAgent($agent);

            $this->em->persist($adherent);
            $this->em->persist($agent);
            $this->em->flush();

            $this->addFlash('success', 'flash.agent.createdSuccessfully');

            return $this->redirectToRoute('lucca_myagent_index');
        }

        return $this->render('@LuccaAdherent/MyAgent/new.html.twig', [
            'agent' => $agent,
            'adherent' => $adherent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Agent entity.
     *
     * @throws Exception
     */
    #[Route(path: '/{id}/edit', name: 'lucca_myagent_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(Request $request, Agent $agent): Response
    {
        /** Find Adherent by connected User */
        $user = $this->tokenStorage->getToken()->getUser();
        $adherent = $this->em->getRepository(Adherent::class)->findOneBy([
            'user' => $user
        ]);

        /** Check if agent is registered in Adherent profile */
        if (!in_array($agent, $adherent->getAgents()->toArray())) {
            throw new Exception('Agent is not registered in your Adherent profile.');
        }

        $editForm = $this->createForm(AgentType::class, $agent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($agent);
            $this->em->flush();

            $this->addFlash('success', 'flash.agent.updatedSuccessfully');

            return $this->redirectToRoute('lucca_myagent_index');
        }

        return $this->render('@LuccaAdherent/MyAgent/edit.html.twig', [
            'agent' => $agent,
            'adherent' => $adherent,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Finds and enable / disable a Agent entity.
     *
     * @throws Exception
     */
    #[Route(path: '/{id}/enable', name: 'lucca_myagent_enable', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function enableAction(Agent $agent): Response
    {
        /** Find Adherent by connected User */
        $user = $this->tokenStorage->getToken()->getUser();
        $adherent = $this->em->getRepository(Adherent::class)->findOneBy([
            'user' => $user
        ]);

        /** Check if agent is registered in Adherent profile */
        if (!in_array($agent, $adherent->getAgents()->toArray())) {
            throw new \Exception('Agent is not registered in your Adherent profile.');
        }

        if ($agent->isEnabled()) {
            $agent->setEnabled(false);
            $this->addFlash('success', 'flash.agent.disabledSuccessfully');
        } else {
            $agent->setEnabled(true);
            $this->addFlash('success', 'flash.agent.enabledSuccessfully');
        }

        $this->em->flush();

        return $this->redirectToRoute('lucca_myagent_index');
    }
}
