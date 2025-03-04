<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Controller\MyAgent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Routing\Annotation\Route;

use Lucca\AdherentBundle\Entity\Agent;
use Lucca\AdherentBundle\Form\AgentType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class AgentController
 *
 * @Security("has_role('ROLE_VISU')")
 * @Route("/my-agent")
 *
 * @package Lucca\AdherentBundle\Controller\MyAgent
 * @author Terence <terence@numeric-wave.tech>
 */

#[\Symfony\Component\Routing\Attribute\Route(path: '/statistics')]
#[IsGranted('ROLE_ADMIN')]
class MyAgentController extends Controller
{
    /**
     * List of Agent
     *
     * @Security("has_role('ROLE_VISU')")
     * @Route("/", name="lucca_myagent_index", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** Find Adherent by connected User */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $adherent = $em->getRepository('LuccaAdherentBundle:Adherent')->findOneBy(array(
            'user' => $user
        ));

        $agents = $em->getRepository('LuccaAdherentBundle:Agent')->findAllByAdherent($adherent);

        return $this->render('LuccaAdherentBundle:MyAgent:index.html.twig', array(
            'user' => $user,
            'agents' => $agents
        ));
    }

    /**
     * Creates a new Agent entity.
     *
     * @Route("/new", name="lucca_myagent_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** Find Adherent by connected User */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $adherent = $em->getRepository('LuccaAdherentBundle:Adherent')->findOneBy(array(
            'user' => $user
        ));

        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adherent->addAgent($agent);

            $em->persist($adherent);
            $em->persist($agent);
            $em->flush();

            $this->addFlash('success', 'flash.agent.createdSuccessfully');
            return $this->redirectToRoute('lucca_myagent_index');
        }

        return $this->render('LuccaAdherentBundle:MyAgent:new.html.twig', array(
            'agent' => $agent,
            'adherent' => $adherent,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Agent entity.
     *
     * @Route("/{id}/edit", name="lucca_myagent_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Agent $agent
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function editAction(Request $request, Agent $agent)
    {
        $em = $this->getDoctrine()->getManager();

        /** Find Adherent by connected User */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $adherent = $em->getRepository('LuccaAdherentBundle:Adherent')->findOneBy(array(
            'user' => $user
        ));

        /** Check if agent is registered in Adherent profile */
        if (!in_array($agent, $adherent->getAgents()->toArray()))
            throw new \Exception('Agent is not registered in your Adherent profile.');

        $editForm = $this->createForm(AgentType::class, $agent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($agent);
            $em->flush();

            $this->addFlash('info', 'flash.agent.updatedSuccessfully');
            return $this->redirectToRoute('lucca_myagent_index');
        }

        return $this->render('LuccaAdherentBundle:MyAgent:edit.html.twig', array(
            'agent' => $agent,
            'adherent' => $adherent,
            'edit_form' => $editForm->createView(),
        ));
    }


    /**
     * Finds and enable / disable a Agent entity.
     *
     * @Route("/{id}/enable", name="lucca_myagent_enable", methods={"GET"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Agent $agent
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function enableAction(Agent $agent)
    {
        $em = $this->getDoctrine()->getManager();

        /** Find Adherent by connected User */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $adherent = $em->getRepository('LuccaAdherentBundle:Adherent')->findOneBy(array(
            'user' => $user
        ));

        /** Check if agent is registered in Adherent profile */
        if (!in_array($agent, $adherent->getAgents()->toArray()))
            throw new \Exception('Agent is not registered in your Adherent profile.');

        if ($agent->isEnabled()) {
            $agent->setEnabled(false);
            $this->addFlash('info', 'flash.agent.disabledSuccessfully');
        } else {
            $agent->setEnabled(true);
            $this->addFlash('info', 'flash.agent.enabledSuccessfully');
        }

        $em->flush();

        return $this->redirectToRoute('lucca_myagent_index');
    }
}
