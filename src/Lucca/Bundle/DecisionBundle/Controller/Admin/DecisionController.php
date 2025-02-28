<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Admin;

use Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Doctrine\ORM\ORMException;
use Lucca\MinuteBundle\Form\DecisionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DecisionController
 *
 * @Security("has_role('ROLE_LUCCA')")
 * @Route("/minute-{minute_id}/decision")
 * @ParamConverter("minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class DecisionController extends Controller
{
    /**
     * Creates a new Decision entity.
     *
     * @Route("/new", name="lucca_decision_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @param Request $request
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    public function newAction(Minute $minute, Request $request)
    {
        $decision = new Decision();
        $decision->setMinute($minute);

        $form = $this->createForm(DecisionType::class, $decision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($decision->getTribunalCommission())
                $decision->getTribunalCommission()->autoDateStartPenalty();

            if ($decision->getAppealCommission())
                $decision->getAppealCommission()->autoDateStartPenalty();

            if ($decision->getCassationComission())
                $decision->getCassationComission()->autoDateStartPenalty();

            /** Call service to clean all html of this step from useless fonts */
            $this->get('lucca.utils.html_cleaner')->cleanHtmlDecision($decision);

            $em->persist($decision);
            $em->persist($minute);
            $em->flush();

            /** update status of the minute */
            $this->get('lucca.manager.minute_story')->manage($minute);
            $em->flush();

            $this->addFlash('success', 'flash.decision.createdSuccessfully');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'decision-' . $decision->getId()));
        }

        return $this->render('LuccaMinuteBundle:Decision:new.html.twig', array(
            'decision' => $decision,
            'minute' => $minute,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Decision entity.
     *
     * @Route("-{id}/edit", name="lucca_decision_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Decision $decision
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Minute $minute, Decision $decision)
    {
        $editForm = $this->createForm(DecisionType::class, $decision);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($decision->getTribunalCommission())
                $decision->getTribunalCommission()->autoDateStartPenalty();

            if ($decision->getAppealCommission())
                $decision->getAppealCommission()->autoDateStartPenalty();

            if ($decision->getCassationComission())
                $decision->getCassationComission()->autoDateStartPenalty();

            /** Call service to clean all html of this step from useless fonts */
            $this->get('lucca.utils.html_cleaner')->cleanHtmlDecision($decision);

            $em->persist($decision);
            $em->flush();

            $this->addFlash('info', 'flash.decision.updatedSuccessfully');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'decision-' . $decision->getId()));
        }

        return $this->render('LuccaMinuteBundle:Decision:edit.html.twig', array(
            'minute' => $minute,
            'decision' => $decision,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Decision entity.
     *
     * @Route("-{id}", name="lucca_decision_delete", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Minute $minute
     * @param Decision $decision
     * @return RedirectResponse
     * @throws ORMException
     */
    public function deleteAction(Minute $minute, Decision $decision)
    {
        $em = $this->getDoctrine()->getManager();

//        if ($decision->getDemolition())
//            $em->remove($decision->getDemolition());
//
//        if ($decision->getExpulsion())
//            $em->remove($decision->getExpulsion());
////
//        if ($decision->getContradictory())
//            $em->remove($decision->getContradictory());

        $em->remove($decision);
        $em->flush();

        $this->get('lucca.manager.minute_story')->manage($minute);
        $em->flush();

        $this->addFlash('danger', 'flash.decision.deletedSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }
}
