<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Controller\Admin;

use App\Lucca\Bundle\MinuteBundle\Manager\MinuteStoryManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Lucca\Bundle\DecisionBundle\Entity\Decision;
use Lucca\Bundle\DecisionBundle\Form\DecisionType;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Utils\{HtmlCleaner};
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_LUCCA')]
#[Route(path: '/minute-{minute_id}/decision')]
class DecisionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HtmlCleaner $htmlCleaner,
        private readonly MinuteStoryManager $minuteStoryManager,
    )
    {
    }

    /**
     * Creates a new Decision entity.
     *
     * @throws ORMException|\DateMalformedStringException
     */
    #[Route(path: '/new', name: 'lucca_decision_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Request $request
    ): Response
    {
        $decision = new Decision();
        $decision->setMinute($minute);

        $form = $this->createForm(DecisionType::class, $decision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($decision->getTribunalCommission()) {
                $decision->getTribunalCommission()->autoDateStartPenalty();
            }

            if ($decision->getAppealCommission()) {
                $decision->getAppealCommission()->autoDateStartPenalty();
            }

            if ($decision->getCassationComission()) {
                $decision->getCassationComission()->autoDateStartPenalty();
            }

            /** Call service to clean all html of this step from useless fonts */
            $this->htmlCleaner->cleanHtmlDecision($decision);

            $this->em->persist($decision);
            $this->em->persist($minute);
            $this->em->flush();

            /** update status of the minute */
            $this->minuteStoryManager->manage($minute);
            $this->em->flush();

            $this->addFlash('success', 'flash.decision.createdSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', [
                'id' => $minute->getId(),
                '_fragment' => 'decision-' . $decision->getId(),
            ]);
        }

        return $this->render('@LuccaDecision/Decision/new.html.twig', [
            'decision' => $decision,
            'minute' => $minute,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Decision entity.
     *
     * @throws \DateMalformedStringException
     */
    #[Route(path: '-{id}/edit', name: 'lucca_decision_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(
        Decision $decision,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Request $request,
    ): Response
    {
        $editForm = $this->createForm(DecisionType::class, $decision);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($decision->getTribunalCommission()) {
                $decision->getTribunalCommission()->autoDateStartPenalty();
            }

            if ($decision->getAppealCommission()) {
                $decision->getAppealCommission()->autoDateStartPenalty();
            }

            if ($decision->getCassationComission()) {
                $decision->getCassationComission()->autoDateStartPenalty();
            }

            /** Call service to clean all html of this step from useless fonts */
            $this->htmlCleaner->cleanHtmlDecision($decision);

            $this->em->persist($decision);
            $this->em->flush();

            $this->addFlash('info', 'flash.decision.updatedSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId(), '_fragment' => 'decision-' . $decision->getId()]);
        }

        return $this->render('@LuccaDecision/Decision/edit.html.twig', [
            'minute' => $minute,
            'decision' => $decision,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Deletes a Decision entity.
     *
     * @throws ORMException
     */
    #[Route(path: '-{id}', name: 'lucca_decision_delete', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(
        Decision $decision,
        #[MapEntity(id: 'minute_id')] Minute $minute,
    ): Response
    {
//        if ($decision->getDemolition())
//            $em->remove($decision->getDemolition());
//
//        if ($decision->getExpulsion())
//            $em->remove($decision->getExpulsion());
////
//        if ($decision->getContradictory())
//            $em->remove($decision->getContradictory());

        $this->em->remove($decision);
        $this->em->flush();

        $this->minuteStoryManager->manage($minute);
        $this->em->flush();

        $this->addFlash('danger', 'flash.decision.deletedSuccessfully');

        return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
    }
}
