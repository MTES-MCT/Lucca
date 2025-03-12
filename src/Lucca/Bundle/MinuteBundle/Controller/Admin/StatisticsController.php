<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\Admin;

use Lucca\Bundle\MinuteBundle\Manager\MinuteStoryManager;
use Lucca\Bundle\MinuteBundle\Manager\StatisticsManager;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\DecisionBundle\Entity\Decision;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Form\Statistics\BrowserMinuteType;
use Lucca\Bundle\MinuteBundle\Form\Statistics\StatsGraphMinuteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/statistics')]
#[IsGranted('ROLE_ADMIN')]
class StatisticsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MinuteStoryManager $minuteStoryManager,
        private readonly StatisticsManager $statisticsManager
    )
    {

    }

    #[Route('/overall', name: 'lucca_statistics_minutes_overall', methods: ['GET', 'POST'])]
    public function overallAction(Request $request): Response
    {
        $em = $this->entityManager;

        $filters = array();

        $form = $this->createForm(StatsGraphMinuteType::class);

        /** If dateStart is not filled - init it */
        if ($form->get('dateStart')->getData() === null)
            $form->get('dateStart')->setData(new \DateTime('first day of January'));
        /** If dateEnd is not filled - init it */
        if ($form->get('dateEnd')->getData() === null)
            $form->get('dateEnd')->setData(new \DateTime('last day of December'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Configure filters with form data */
            $filters['dateStart'] = new \DateTime($form->get('dateStart')->getData()->format('Y-m-d') . ' 00:00');
            $filters['dateEnd'] = new \DateTime($form->get('dateEnd')->getData()->format('Y-m-d') . ' 23:59');

            $filters['adherent'] = $form->get('adherent')->getData();
            $filters['town'] = $form->get('town')->getData();
            $filters['townAdherent'] = $form->get('townAdherent')->getData();
            $filters['intercommunal'] = $form->get('intercommunal')->getData();
            $filters['service'] = $form->get('service')->getData();
        } else {
            /** Init default filters */
            $filters['dateStart'] = new \DateTime($form->get('dateStart')->getData()->format('Y-m-d') . ' 00:00');
            $filters['dateEnd'] = new \DateTime($form->get('dateEnd')->getData()->format('Y-m-d') . ' 23:59');

            $filters['adherent'] = null;
            $filters['townAdherent'] = null;
            $filters['town'] = null;
            $filters['intercommunal'] = null;
            $filters['service'] = null;
        }

        /** First start by finding all minute that correspond to filter in order to use filter only in one request */
        $minutes = $em->getRepository(Minute::class)->statMinuteOverall(
            $filters['dateStart'], $filters['dateEnd'], $filters['adherent'], $filters['town'],
            $filters['intercommunal'], $filters['service'], $filters['townAdherent']
        );

        if (count($minutes) > 0) {
            /** Set up the closure array in order to display the graph 'Reason of closure' */
            $closures = $this->minuteStoryManager->manageClosureData($filters['dateStart'], $filters['dateEnd'], $minutes);

            /** Set up the $closeUnclosed array in order to display counter of close and unclosed minutes */
            $closeUnclosed = $this->statisticsManager->createCloseUnclosedArray($minutes);

            /** Find all the needed data that are linked to minute we can find in the minutes array */
            /** Used to display graph 'Type of decisions' */
            $decisionsTypeCounters = $em->getRepository(Decision::class)->countTypesBetweenDates($minutes);
            /** Used to display graph 'Decisions' */
            $decisionsCounters = $em->getRepository(Decision::class)->countBetweenDates($minutes);

            /** Find all the needed data that are linked to minute we can find in the minutes array */
            $folders = $em->getRepository(Folder::class)->findBetweenDates($minutes);

            /** Number of folder without Natinfs */
            $nbFolderWithEmptyNatinfs = $this->statisticsManager->countFolderWithoutNatinfs($folders);

            /** We need to get data for all state of control in this stat */
            $controls = $em->getRepository(Control::class)->findBetweenDates($minutes, [Control::STATE_INSIDE ,Control::STATE_INSIDE_WITHOUT_CONVOCATION, Control::STATE_NEIGHBOUR, Control::STATE_OUTSIDE]);
            $decisions = $em->getRepository(Decision::class)->findBetweenDates($minutes);

            /** Create the final array used to display histogram 'Historic' */
            $finalArray = $this->statisticsManager->createOverallArray($filters['dateStart'], $filters['dateEnd'],
                $folders, $controls, $decisions);
        } else {
            /** If minute array is empty init all result array to empty array */
            $closures = null;
            $closeUnclosed['unclosed'] = null;
            $closeUnclosed['closed'] = null;
            $decisionsTypeCounters = null;
            $decisionsCounters = null;
            $nbFolderWithEmptyNatinfs = null;
            $finalArray = null;
        }

        return $this->render('@LuccaMinute/Statistics/overall.html.twig', array(
            'form' => $form->createView(),
            'filters' => $filters,
            'closures' => $closures,
            'nbFolderWithEmptyNatinfs' => $nbFolderWithEmptyNatinfs,
            'nbOpen' => $closeUnclosed['unclosed'],
            'nbClosed' => $closeUnclosed['closed'],
            'decisionsTypeCounters' => $decisionsTypeCounters,
            'decisionsCounters' => $decisionsCounters,
            'finalArray' => $finalArray,
        ));
    }

    #[Route('/minutes', name: 'lucca_statistics_minutes_table', methods: ['GET', 'POST'])]
    public function minutesAction(Request $request): Response
    {
        $em = $this->entityManager;;

        $filters = array();

        $form = $this->createForm(BrowserMinuteType::class);

        /** If dateStart is not filled - init it */
        if ($form->get('dateStart')->getData() === null)
            $form->get('dateStart')->setData(new \DateTime('first day of January'));
        /** If dateEnd is not filled - init it */
        if ($form->get('dateEnd')->getData() === null)
            $form->get('dateEnd')->setData(new \DateTime('last day of December'));

        $form->handleRequest($request);

        /**
         * Configure filters with form data,
         * we can use form date because we prefilled date and empty field are defined with null
         */
        $filters['dateStart'] = $form->get('dateStart')->getData();
        $filters['dateEnd'] = $form->get('dateEnd')->getData();
        $filters['adherent'] = $form->get('adherent')->getData();
        $filters['town'] = $form->get('town')->getData();
        $filters['intercommunal'] = $form->get('intercommunal')->getData();
        $filters['service'] = $form->get('service')->getData();
        $filters['origin'] = $form->get('origin')->getData();
        $filters['risk'] = $form->get('risk')->getData();
        $filters['stateControl'] = $form->get('stateControl')->getData();
        $filters['nature'] = $form->get('nature')->getData();
        $filters['natinfs'] = $form->get('natinfs')->getData();
        $filters['dateStartClosure'] = $form->get('dateStartClosure')->getData();
        $filters['dateEndClosure'] = $form->get('dateEndClosure')->getData();
        $filters['townAdherent'] = $form->get('townAdherent')->getData();

        if (!$this->statisticsManager->checkDatesFilters($filters)) {
            $this->addFlash('warning', 'flash.filters.datesIncorrect');

            /** Init default filters */
            $filters['dateStart'] = new \DateTime('first day of January');
            $filters['dateEnd'] = new \DateTime('last day of December');

            $filters['adherent'] = null;
            $filters['town'] = null;
            $filters['intercommunal'] = null;
            $filters['service'] = null;
            $filters['origin'] = null;
            $filters['risk'] = null;
            $filters['stateControl'] = null;
            $filters['nature'] = null;
            $filters['natinfs'] = null;
            $filters['dateStartClosure'] = null;
            $filters['dateEndClosure'] = null;
            $filters['townAdherent'] = null;
        }

        /** First get all controls filtered */
        if ($filters['stateControl'] !== null)
            $controls = $em->getRepository(Control::class)->statControl($filters['stateControl']);
        else
            $controls = null;

        /** Test controls in order to look for folder only if we didn't use filter on controls or if there is some controls returned */
        if (!empty($controls) || $controls == null) {
            /** Then get all folders filtered and based on controls selected */
            if (
                $filters['natinfs'] !== null && count($filters['natinfs']) > 0 ||
                $filters['nature'] !== null && count($filters['nature']) > 0 ||
                $filters['dateStartClosure'] !== null ||
                $filters['dateEndClosure'] !== null
            ) {
                $folders = $em->getRepository(Folder::class)->statFolders(
                    $filters['natinfs'], $filters['nature'], $filters['dateStartClosure'], $filters['dateEndClosure'], $controls
                );
            } else {
                $folders = null;
            }
        } else {
            $folders = array();
        }

        /** Test folders in order to look for minutes only if we didn't use filter on folders or if there is some folders returned */
        if (!empty($folders) || $folders === null) {
            /** Then get all minutes filtered and based on folders selected */
            $minutes = $em->getRepository(Minute::class)->statMinutes(
                $filters['dateStart'], $filters['dateEnd'], $filters['adherent'], $filters['town'], $filters['intercommunal'],
                $filters['service'], $filters['origin'], $filters['risk'], $filters['townAdherent'], $folders
            );

            /** Create an array containing data about each minutes */
            $data = $this->statisticsManager->createMinuteDataArray($minutes);
        } else {
            $minutes = array();
            $data = array();
        }

        return $this->render('@LuccaMinute/Statistics/minutes.html.twig', array(
            'form' => $form->createView(),
            'filters' => $filters,
            'minutes' => $minutes,
            'data' => $data,
        ));
    }
}
