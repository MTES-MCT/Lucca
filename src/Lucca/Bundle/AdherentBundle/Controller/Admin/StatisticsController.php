<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Controller\Admin;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Form\Statistics\StatisticsAdherentType;
use Lucca\Bundle\AdherentBundle\Manager\StatisticsManager;
use Lucca\Bundle\MinuteBundle\Entity\Minute;

#[Route(path: '/statistics')]
#[IsGranted('ROLE_ADMIN')]
class StatisticsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly StatisticsManager $statisticsManager,
    )
    {
    }

    /**
     * Statistics on adherent and city by intercommunal / adherent / city organised in graph
     *
     * @throws Exception
     */
    #[Route(path: '/adherents', name: 'lucca_statistics_adherents')]
    #[IsGranted('ROLE_ADMIN')]
    public function adherentAction(Request $request): Response
    {
        /** Init default filters */
        $filters = [];

        $form = $this->createForm(StatisticsAdherentType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Configure filters with form data */
            $filters['adherent'] = $form->get('adherent')->getData();
            $filters['town'] = $form->get('town')->getData();
            $filters['intercommunal'] = $form->get('intercommunal')->getData();
            $filters['service'] = $form->get('service')->getData();
        } else {
            /** Init default filters */
            $filters['adherent'] = null;
            $filters['town'] = null;
            $filters['intercommunal'] = null;
            $filters['service'] = null;
        }

        $adherents = $this->em->getRepository(Adherent::class)->statAdherent($filters['adherent'], $filters['town'], $filters['intercommunal'], $filters['service']);
        /** If we can't find adherent don't try to find minutes */
        $minutes = [];
        if (count($adherents) > 0) {
            $minutes = $this->em->getRepository(Minute::class)->statMinutesByAdherents($adherents);
        }

        $finalArray = $this->statisticsManager->createStatArray($adherents, $minutes);

        return $this->render('@LuccaAdherent/Statistics/adherents.html.twig', [
            'form' => $form->createView(),
            'filters' => $filters,
            'finalArray' => $finalArray,
        ]);
    }
}
