<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\Admin;

use Lucca\Bundle\MinuteBundle\Manager\ClosureManager;
use Lucca\Bundle\MinuteBundle\Manager\MinuteStoryManager;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\MinuteBundle\Entity\Closure;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Form\ClosureType;
use Lucca\Bundle\MinuteBundle\Utils\HtmlCleaner;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/minute')]
#[IsGranted('ROLE_LUCCA')]
class ClosureController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MinuteStoryManager $minuteStoryManager,
        private readonly ClosureManager $closureManager,
        private readonly htmlCleaner $htmlCleaner
    )
    {
    }

    #[Route('-{id}/close', name: 'lucca_minute_close', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function closeAction(Request $request, #[MapEntity(id: 'id')] Minute $minute): RedirectResponse|Response|null
    {
        $closure = new Closure();
        $closure->setMinute($minute);

        $form = $this->createForm(ClosureType::class, $closure, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->entityManager;

            $this->closureManager->closeMinute($minute);

            /** Call service to clean all html of this step from useless fonts */
            $closure->setObservation($this->htmlCleaner->removeAllFonts($closure->getObservation()));

            $em->persist($closure);
            $em->persist($minute);
            $em->flush();

            /** update status of the minute */
            $this->minuteStoryManager->manage($minute);
            $em->flush();

            $this->addFlash('info', 'flash.closure.closedSuccessfully');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'closure-' . $closure->getId()));
        }

        return $this->render('@LuccaMinute/Closure/close.html.twig', array(
            'minute' => $minute,
            'closure' => $closure,
            'form' => $form->createView(),
        ));
    }

    #[Route('-{id}/open', name: 'lucca_minute_open', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function openAction(#[MapEntity(id: 'id')] Minute $minute): RedirectResponse
    {
        $em = $this->entityManager;;
        $closure = $em->getRepository(Closure::class)->findOneBy(array(
            'minute' => $minute
        ));
        $closure->setMinuteOpen($minute);

        $em->persist($minute);

        $em->remove($closure);
        $em->flush();

        /** update status of the minute */
        $this->minuteStoryManager->manage($minute);
        $em->flush();

        $this->addFlash('warning', 'flash.closure.openSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }
}
