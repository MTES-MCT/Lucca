<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\ORMException;

use Lucca\Bundle\DecisionBundle\Entity\Decision;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\DecisionBundle\Form\DecisionType;


#[IsGranted('ROLE_LUCCA')]
#[Route('/minute-{minute_id}/decision')]
class DecisionController extends AbstractController
{
    /**
     * Creates a new Decision entity.
     *
     * @param Minute $minute
     * @param Request $request
     * @return RedirectResponse|Response|null
     * @throws ORMException|\DateMalformedStringException
     */
    #[Route('/new', name: 'lucca_decision_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(Minute $minute, Request $request): RedirectResponse|Response|null
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

        return $this->render('@LuccaDecision/Decision/new.html.twig', array(
            'decision' => $decision,
            'minute' => $minute,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Decision entity.
     *
     * @param Request $request
     * @param Minute $minute
     * @param Decision $decision
     * @return RedirectResponse|Response
     * @throws \DateMalformedStringException
     */
    #[Route('-{id}/edit', name: 'lucca_decision_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(Request $request, Minute $minute, Decision $decision): RedirectResponse|Response
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

        return $this->render('@LuccaDecision/Decision/edit.html.twig', array(
            'minute' => $minute,
            'decision' => $decision,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Decision entity.
     *
     * @param Minute $minute
     * @param Decision $decision
     * @return RedirectResponse
     * @throws ORMException
     */
    #[Route('-{id}', name: 'lucca_decision_delete', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Minute $minute, Decision $decision): RedirectResponse
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
