<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\FolderBundle\Form\CourierType;
use Lucca\Bundle\FolderBundle\Entity\Courier;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Utils\HtmlCleaner;
use Lucca\Bundle\MinuteBundle\Manager\MinuteStoryManager;
use Lucca\Bundle\CoreBundle\Exception\AigleNotificationException;
use Lucca\Bundle\CoreBundle\Service\Aigle\MinuteChangeStatusAigleNotifier;

#[IsGranted('ROLE_LUCCA')]
#[Route(path: '/minute-{minute_id}/courier')]
class CourierController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface          $em,
        private readonly HtmlCleaner                     $htmlCleaner,
        private readonly MinuteStoryManager              $minuteStoryManager,
        private readonly MinuteChangeStatusAigleNotifier $minuteChangeStatusAigleNotifier,
        private readonly TranslatorInterface             $translator
    )
    {
    }

    /**
     * Judicial Date
     *
     * @throws ORMException
     */
    #[IsGranted('ROLE_LUCCA')]
    #[Route(path: '-{id}/judicial-date', name: 'lucca_courier_judicial_date', methods: ['GET', 'POST'])]
    public function judicialDateAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Courier $courier,
        Request $request,
    ): Response
    {
        $form = $this->createForm(CourierType::class, $courier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Clean html from useless font and empty span */
            $courier->setContext($this->htmlCleaner->removeAllFonts($courier->getContext()));

            $this->em->persist($courier);
            $this->em->flush();

            $this->addFlash('success', 'flash.courier.judicialUpdate');

            /** update status of the minute */
            $this->minuteStoryManager->manage($minute);
            $this->em->flush();

            try {
                $this->minuteChangeStatusAigleNotifier->updateAigleMinuteStatus($minute);
            } catch (AigleNotificationException $e) {
                $this->addFlash('danger', $e->getTranslatedMessage($this->translator));
            }

            if ($request->request->get('saveAndContinue') !== null) {
                return $this->redirectToRoute('lucca_courier_manual_judicial', [
                    'minute_id' => $minute->getId(), 'id' => $courier->getId()
                ]);
            }

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return $this->render('@LuccaFolder/Courier/judicial.html.twig', [
            'minute' => $minute,
            'courier' => $courier,
            'form' => $form->createView(),
        ]);
    }
}
