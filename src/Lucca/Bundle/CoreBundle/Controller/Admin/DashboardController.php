<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Controller\Admin;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Service\AdherentFinder;
use Lucca\Bundle\CoreBundle\Form\Admin\BrowserDashboardType;
use Lucca\Bundle\MinuteBundle\Manager\MinuteStoryManager;
use Lucca\Bundle\MinuteBundle\Entity\MinuteStory;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

#[Route(path: '/admin')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MinuteStoryManager     $minuteStoryManager,
        private readonly AdherentFinder         $adherentFinder,
    )
    {
    }

    /**
     * Admin dashboard we basic stats
     *
     * @throws \Exception
     */
    #[Route(path: 'dashboard', name: 'lucca_admin_dashboard', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function dashboardAction(Request $request): ?Response
    {
        /** Who is connected */
        $adherent = $this->adherentFinder->whoAmI();

        /** Check if the user can access to the dashboard admin */
        if($adherent->getFunction() != Adherent::FUNCTION_COUNTRY_AGENT || SettingManager::get('setting.module.dashboardAdmin.name') == false) {
            $this->addFlash('danger', 'flash.adherent.accessDenied');

            return $this->redirectToRoute('lucca_core_dashboard');
        }

        /** Init default filters */
        $filters = array();

        $form = $this->createForm(BrowserDashboardType::class);

        /** If dateStart is not filled - init it */
        if ($form->get('dateStart')->getData() === null) {
            $form->get('dateStart')->setData(new DateTime('first day of January'));
        }

        /** If dateEnd is not filled - init it */
        if ($form->get('dateEnd')->getData() === null) {
            $form->get('dateEnd')->setData(new DateTime('last day of December'));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Get data from form */
            $filters['dateStart'] = new DateTime($form->get('dateStart')->getData()->format('Y-m-d') . ' 00:00');
            $filters['dateEnd'] = new DateTime($form->get('dateEnd')->getData()->format('Y-m-d') . ' 23:59');
        } else {
            /** Configure filters with default data */
            $filters['dateStart'] = new DateTime($form->get('dateStart')->getData()->format('Y-m-d') . ' 00:00');
            $filters['dateEnd'] = new DateTime($form->get('dateEnd')->getData()->format('Y-m-d') . ' 23:59');
        }

        $minutesStories = $this->em->getRepository(MinuteStory::class)->findLast($filters['dateStart'], $filters['dateEnd']);

        /** Create the final array with last story for each sorted in array for each status */
        $stories = $this->minuteStoryManager->keepLastStories($minutesStories, $filters['dateStart'], $filters['dateEnd']);
        $closures = $this->minuteStoryManager->manageClosureData($filters['dateStart'], $filters['dateEnd']);

        /** Only render the view because all treatment is done with js and api */
        return $this->render('@LuccaCore/Admin/dashboard.html.twig', array(
            'form' => $form->createView(),
            'adherentId' => $adherent->getId(),
            'dateStart' => $filters['dateStart'],
            'dateEnd' => $filters['dateEnd'],
            'stories' => $stories,
            'closures' => $closures,
        ));
    }
}
