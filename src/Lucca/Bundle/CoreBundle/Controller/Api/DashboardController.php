<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\CoreBundle\Service\GeoLocator;
use Lucca\Bundle\DecisionBundle\Entity\Decision;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\{Closure, Control, Minute, MinuteStory, Plot, Updating};
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

#[Route(path: '/')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly AdherentFinder $adherentFinder,
        private readonly GeoLocator $geoLocator,
    )
    {
    }

    /**
     * Reformat data to use it in view
     */
    private function formatSpotting($spotting, $displayResults): array
    {
        $data = [];

        foreach ($spotting as $minute) {
            if ($displayResults === "true") {
                $dataFolder['url'] = $this->generateUrl('lucca_minute_show', ['id' => $minute->getId()]);
                $dataFolder['agent'] = $minute->getAgent()->getOfficialName();
            }

            $dataFolder['num'] = $minute->getNum();
            $dataFolder['address'] = $minute->getPlot()->getFullAddress();

            array_push($data, $dataFolder);
        }

        return $data;
    }

    /**
     * Reformat data to use it in view
     */
    private function formatFolderOpen($foldersOpen, $displayResults): array
    {
        $data = [];

        foreach ($foldersOpen as $minute) {
            if ($displayResults === "true") {
                $dataFolder['url'] = $this->generateUrl('lucca_minute_show', ['id' => $minute->getId()]);
                $dataFolder['agent'] = $minute->getAgent()->getOfficialName();
            }

            $dataFolder['num'] = $minute->getNum();
            $dataFolder['address'] = $minute->getPlot()->getFullAddress();

            array_push($data, $dataFolder);
        }

        return $data;
    }

    /**
     * Reformat data to use it in view
     */
    private function formatFolderClosed($foldersClosed, $displayResults): array
    {
        $data = [];

        foreach ($foldersClosed as $minute) {
            if ($displayResults === "true") {
                $dataFolder['url'] = $this->generateUrl('lucca_minute_show', ['id' => $minute->getId()]);
                $dataFolder['agent'] = $minute->getAgent()->getOfficialName();
            }

            $dataFolder['num'] = $minute->getNum();
            $dataFolder['address'] = $minute->getPlot()->getFullAddress();

            array_push($data, $dataFolder);
        }

        return $data;
    }

    /**
     * Reformat data to use it in view
     */
    private function formatFolderClosedOther($foldersClosedOther, $displayResults): array
    {
        $data = [];

        foreach ($foldersClosedOther as $minute) {
            if ($displayResults === "true") {
                $dataFolder['url'] = $this->generateUrl('lucca_minute_show', ['id' => $minute->getId()]);
                $dataFolder['agent'] = $minute->getAgent()->getOfficialName();
            }

            $dataFolder['num'] = $minute->getNum();
            $dataFolder['address'] = $minute->getPlot()->getFullAddress();

            array_push($data, $dataFolder);
        }

        return $data;
    }

    /**
     * Reformat data to use it in view
     */
    private function formatControl($controls, $displayResults): array
    {
        $data = [];

        foreach ($controls as $control) {
            if ($displayResults === "true") {
                $dataControl['url'] = $this->generateUrl('lucca_minute_show', [
                    'id' => $control->getMinute()->getId(),
                    '_fragment' => 'control-' . $control->getId(),
                ]);
                $dataControl['agent'] = $control->getAgent()->getOfficialName();
            }

            $dataControl['num'] = $control->getMinute()->getNum();
            $dataControl['address'] = $control->getMinute()->getPlot()->getFullAddress();

            array_push($data, $dataControl);
        }

        return $data;
    }

    /**
     * Reformat data to use it in view
     */
    private function formatDecisions($decisions, $displayResults): array
    {
        $data = [];

        foreach ($decisions as $decision) {
            if ($displayResults === "true") {
                $dataControl['url'] = $this->generateUrl('lucca_minute_show', [
                    'id' => $decision->getMinute()->getId(),
                    '_fragment' => 'decision-' . $decision->getId(),
                ]);
                $dataControl['agent'] = $decision->getMinute()->getAgent()->getOfficialName();
            }

            $dataControl['num'] = $decision->getMinute()->getNum();
            $dataControl['address'] = $decision->getMinute()->getPlot()->getFullAddress();

            array_push($data, $dataControl);
        }

        return $data;
    }

    /**
     * Reformat data to use it in view
     */
    private function formatFolders($folders, $displayResults): array
    {
        $data = [];

        foreach ($folders as $folder) {
            if ($displayResults === "true") {
                $dataFolder['url'] = $this->generateUrl('lucca_minute_show', [
                    'id' => $folder->getMinute()->getId(),
                    '_fragment' => 'folder-' . $folder->getId(),
                ]);
                $dataFolder['agent'] = $folder->getMinute()->getAgent()->getOfficialName();
            }

            $dataFolder['num'] = $folder->getNum();
            $dataFolder['address'] = $folder->getMinute()->getPlot()->getFullAddress();

            array_push($data, $dataFolder);
        }

        return $data;
    }

    /**
     * Reformat data to use it in view
     */
    private function formatRedo($redo, $displayResults): array
    {
        $data = [];

        foreach ($redo as $updating) {
            if ($displayResults === "true") {
                $dataFolder['url'] = $this->generateUrl('lucca_minute_show', [
                    'id' => $updating->getMinute()->getId(),
                    '_fragment' => 'updating-' . $updating->getId(),
                ]);
                $dataFolder['agent'] = $updating->getMinute()->getAgent()->getOfficialName();
            }

            $dataFolder['num'] = $updating->getNum();
            $dataFolder['address'] = $updating->getMinute()->getPlot()->getFullAddress();

            array_push($data, $dataFolder);
        }

        return $data;
    }

    /**
     * Search list of wine depends of text typed
     */
    #[Route(path: '/list', name: 'lucca_find_address', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function findAddress(Request $request): Response|JsonResponse
    {
        $addr = $request->get('address');
        $displayResults = $request->get('displayResults');

        /** Who is connected ;) */
        $adherent = $this->adherentFinder->whoAmI();

        $result = $this->geoLocator->getGeocodeFromAddress($addr);

        if (!$result || $result['latitude'] === null || $result['longitude'] === null) {
            return new JsonResponse([
                'success' => false,
                'code' => 400,
                'message' => $addr
            ], 400);
        }

        $data['geoCode']['lat'] = $result['latitude'];
        $data['geoCode']['lon'] = $result['longitude'];

        $radius = 0.02;
        $latMin = $data['geoCode']['lat'] - $radius;
        $latMax = $data['geoCode']['lat'] + $radius;
        $lonMin = $data['geoCode']['lon'] - $radius;
        $lonMax = $data['geoCode']['lon'] + $radius;

        /** Find all folder open and move object in spotting array if there is no documents link to the minute */
        $foldersOpen = $this->em->getRepository(Minute::class)->findAllInArea($latMin, $latMax, $lonMin, $lonMax, $adherent);
        $spotting = [];

        foreach ($foldersOpen as $ind => $minute) {
            if ($minute->getControls()->isEmpty() and $minute->getUpdatings()->isEmpty() and $minute->getDecisions()->isEmpty()) {
                array_push($spotting, $minute);
                unset($foldersOpen[array_search($minute, $foldersOpen)]);
            }
        }

        /** Add max results to the request to avoid map to crash due to quantity of data */
        $foldersClosed = $this->em->getRepository(Minute::class)->findAllInArea($latMin, $latMax, $lonMin, $lonMax, $adherent, [Closure::STATUS_REGULARIZED]);
        $foldersClosedOther = $this->em->getRepository(Minute::class)->findAllInArea($latMin, $latMax, $lonMin, $lonMax, $adherent, [Closure::STATUS_EXEC_OFFICE, Closure::STATUS_OTHER, Closure::STATUS_RELAXED]);
        $controls = $this->em->getRepository(Control::class)->findAllInArea($latMin, $latMax, $lonMin, $lonMax, $adherent);
        $decisions = $this->em->getRepository(Decision::class)->findAllInArea($latMin, $latMax, $lonMin, $lonMax, $adherent);
        $folders = $this->em->getRepository(Folder::class)->findAllInArea($latMin, $latMax, $lonMin, $lonMax, $adherent);
        $redo = $this->em->getRepository(Updating::class)->findAllInArea($latMin, $latMax, $lonMin, $lonMax, $adherent);

        /**
         *  Add in tab data the list of spotting in area
         */
        $data['spotting'] = $this->formatSpotting($spotting, $displayResults);

        /**
         *  Add in tab data the list of folder open in area
         */
        $data['foldersOpen'] = $this->formatFolderOpen($foldersOpen, $displayResults);

        /**
         *  Add in tab data the list of folder closed in area with specific state of closure
         */
        $data['foldersClosed'] = $this->formatFolderClosed($foldersClosed, $displayResults);

        /**
         *  Add in tab data the list of folder closed in area with specific state of closure
         */
        $data['foldersClosedOther'] = $this->formatFolderClosedOther($foldersClosedOther, $displayResults);

        /**
         *  Add in tab data the list of control in area
         */
        $data['controls'] = $this->formatControl($controls, $displayResults);

        /**
         *  Add in tab data the list of decisions in area
         */
        $data['decisions'] = $this->formatDecisions($decisions, $displayResults);

        /**
         *  Add in tab data the list of folder in area
         */
        $data['folders'] = $this->formatFolders($folders, $displayResults);

        /**
         *  Add in tab data the list of updating in area
         */
        $data['redo'] = $this->formatRedo($redo, $displayResults);

        return new Response(json_encode($data), 200);
    }

    /**
     * Create folder when right click on map
     */
    #[Route(path: '/create', name: 'lucca_create_folder', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function createFolder(Request $request): Response
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');

        $address = $this->geoLocator->getAddressFromGeocode($lat, $lng);
        if (!$address or !is_array($address)) {
            return new JsonResponse([
                'success' => false,
                'code' => 400,
                'message' => "Erreur dans la récupération de l'addresse aux coordonées : latitude " . $lat . " et longitude " . $lng
            ], 400);
        }
        /** Put data in session to avoid pass it in url */
        $_SESSION['addrRoute'] = $address['addrRoute'];
        $_SESSION['addrCity'] = $address['addrCity'];
        $_SESSION['addrCode'] = $address['addrCode'];
        $data['url'] = $this->generateUrl('lucca_minute_new');
        $data['text'] = $this->translator->trans('text.createFolderGeolocalized', [], 'CoreBundle');
        $data['addr'] = $address['addrRoute'] . ' ' . $address['addrCity'] . ' ' . $address['addrCode'];
        return new Response(json_encode($data), 200);
    }

    /*************************** Asynchronous load in map view *********************************************************/
    /**
     * Get all the controls
     * Used to display markers on the map
     */
    #[Route(path: '/getControls', name: 'lucca_get_controls', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getControls(Request $request): Response
    {
        $displayResults = $request->get('displayResults');

        /** Coords of the border of the map visible */
        $minLat = $request->get('minLat');
        $minLon = $request->get('minLon');
        $maxLat = $request->get('maxLat');
        $maxLon = $request->get('maxLon');
        /** Use full for admin dashboard */
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        if ($request->get('adherent'))
            $adherent = $this->em->getRepository(Adherent::class)->find($request->get('adherent'));
        else
            $adherent = null;

        /** In order to use the same logic in all map get the date start and end only in the admin dashboard */
        if ($dateStart) {
            $minutes = $this->getMinutesBetweenDates($dateStart, $dateEnd, MinuteStory::STATUS_CONTROL);

            /** Then is there is minutes use it to find the corresponding entities */
            if (count($minutes) < 1) {
                /** If the minute array is empty, return an empty folder array */
                $controls = [];
            } else {
                $controls = $this->em->getRepository(Control::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent, SettingManager::get('setting.map.maxResults.name'), $minutes);
            }
        } else {
            $controls = $this->em->getRepository(Control::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent, SettingManager::get('setting.map.maxResults.name'));
        }
        $data = [];

        foreach ($controls as $control) {
            $dataControl['id'] = $control->getId();
            $dataControl['address'] = $control->getMinute()->getPlot()->getFullAddress();
            $dataControl['lat'] = floatval($control->getMinute()->getPlot()->getLatitude());
            $dataControl['lon'] = floatval($control->getMinute()->getPlot()->getLongitude());
            $dataControl['parcel'] = $control->getMinute()->getPlot()->getParcel();
            if ($control->getDateControl()) {
                $dataControl['date'] = $control->getDateControl()->format('d/m/Y');
            }
            else {
                $dataControl['date'] = "";
            }
            $dataControl['agentOfficialName'] = $control->getAgent()->getOfficialName();
            $dataControl['minuteNum'] = $control->getMinute()->getNum();
            $dataControl['minuteId'] = $control->getMinute()->getId();

            if ($displayResults) {
                $dataControl['humans'] = "";

                foreach ($control->getHumansByControl() as $human) {
                    $dataControl['humans'] .= ($human->getOfficialName() . ' ');
                }

                foreach ($control->getHumansByMinute() as $human) {
                    $dataControl['humans'] .= ($human->getOfficialName() . ' ');
                }

            }
            array_push($data, $dataControl);
        }

        return new Response(json_encode($data), 200);
    }

    /**
     * Get all the Decisions
     * Used to display markers on the map
     */
    #[Route(path: '/getDecisions', name: 'lucca_get_decisions', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getDecisions(Request $request): Response
    {
        /** Coords of the border of the map visible */
        $minLat = $request->get('minLat');
        $minLon = $request->get('minLon');
        $maxLat = $request->get('maxLat');
        $maxLon = $request->get('maxLon');
        /** Use full for admin dashboard */
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        $displayResults = $request->get('displayResults');
        if ($request->get('adherent'))
            $adherent = $this->em->getRepository(Adherent::class)->find($request->get('adherent'));
        else
            $adherent = null;

        /** In order to use the same logic in all map get the date start and end only in the admin dashboard */
        if ($dateStart) {
            $minutes = $this->getMinutesBetweenDates($dateStart, $dateEnd, MinuteStory::STATUS_DECISION);

            /** Then is there is minutes use it to find the corresponding entities */
            if (count($minutes) < 1) {
                /** If the minute array is empty, return an empty folder array */
                $decisions = [];
            } else {
                $decisions = $this->em->getRepository(Decision::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent, SettingManager::get('setting.map.maxResults.name'), $minutes);
            }
        } else {
            $decisions = $this->em->getRepository(Decision::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent, SettingManager::get('setting.map.maxResults.name'));
        }

        $data = [];

        foreach ($decisions as $decision) {
            $dataDecision['id'] = $decision->getId();
            $dataDecision['lat'] = floatval($decision->getMinute()->getPlot()->getLatitude());
            $dataDecision['lon'] = floatval($decision->getMinute()->getPlot()->getLongitude());
            $dataDecision['parcel'] = $decision->getMinute()->getPlot()->getParcel();
            $dataDecision['minuteId'] = $decision->getMinute()->getId();
            $dataDecision['minuteNum'] = $decision->getMinute()->getNum();
            $dataDecision['appeal'] = $decision->getAppeal();
            if ($decision->getTribunalCommission()) {
                $dataDecision['TCstatusDecision'] = $this->translator->trans($decision->getTribunalCommission()->getStatusDecision(), [], 'MinuteBundle');
            }
            if ($decision->getTribunalCommission() and $decision->getTribunalCommission()->getDateHearing())
                $dataDecision['TCdateHearing'] = $decision->getTribunalCommission()->getDateHearing()->format('d/m/Y');
            else
                $dataDecision['TCdateHearing'] = "";

            if ($decision->getAmountPenaltyDaily())
                $dataDecision['amountPenaltyDaily'] = $decision->getAmountPenaltyDaily();
            else
                $dataDecision['amountPenaltyDaily'] = "";

            if ($displayResults) {
                $dataDecision['humans'] = "";

                $date = new \DateTime('now');
                foreach ($decision->getPenalties() as $index => $penalty) {
                    if ($index == 0 or $date > $penalty->getDateFolder()) {
                        $date = $penalty->getDateFolder();
                        $dataDecision['state'] = $this->translator->trans($penalty->getNature(), [], 'MinuteBundle');
                    }
                }
                $dataDecision['date'] = $date->format('d/m/Y');
            }

            array_push($data, $dataDecision);
        }

        return new Response(json_encode($data), 200);
    }

    /**
     * Get all the Folders
     * Used to display markers on the map
     *
     * @throws \Exception
     */
    #[Route(path: '/getFolders', name: 'lucca_get_folders', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getFolders(Request $request): Response
    {
        /** Coords of the border of the map visible */
        $minLat = $request->get('minLat');
        $minLon = $request->get('minLon');
        $maxLat = $request->get('maxLat');
        $maxLon = $request->get('maxLon');
        /** Use full for admin dashboard */
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        $displayResults = $request->get('displayResults');
        if ($request->get('adherent')) {
            $adherent = $this->em->getRepository(Adherent::class)->find($request->get('adherent'));
        }
        else {
            $adherent = null;
        }

        /** In order to use the same logic in all map get the date start and end only in the admin dashboard */
        if ($dateStart) {
            $minutes = $this->getMinutesBetweenDates($dateStart, $dateEnd, MinuteStory::STATUS_FOLDER);

            /** Then is there is minutes use it to find the corresponding entities */
            if (count($minutes) < 1) {
                /** If the minute array is empty, return an empty folder array */
                $folders = [];
            } else {
                $folders = $this->em->getRepository(Folder::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent, SettingManager::get('setting.map.maxResults.name'), $minutes);
            }
        } else {
            /** If there is no start date keep the logic in place */
            $folders = $this->em->getRepository(Folder::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent, SettingManager::get('setting.map.maxResults.name'));
        }
        $data = [];

        foreach ($folders as $folder) {
            $dataFolder['id'] = $folder->getId();
            $dataFolder['minuteId'] = $folder->getMinute()->getId();
            $dataFolder['minuteNum'] = $folder->getMinute()->getNum();
            $dataFolder['lat'] = floatval($folder->getMinute()->getPlot()->getLatitude());
            $dataFolder['lon'] = floatval($folder->getMinute()->getPlot()->getLongitude());
            $dataFolder['parcel'] = $folder->getMinute()->getPlot()->getParcel();
            $dataFolder['nature'] = $this->translator->trans($folder->getNature(), [], 'MinuteBundle');
            if ($folder->getControl() && $folder->getControl()->getDateControl())
                $dataFolder['dateControl'] = $folder->getControl()->getDateControl()->format('d/m/Y');
            else
                $dataFolder['dateControl'] = null;

            if ($folder->getDateClosure())
                $dataFolder['dateClosure'] = $folder->getDateClosure()->format('d/m/Y');
            else
                $dataFolder['dateClosure'] = null;

            if ($displayResults) {
                $dataFolder['tagsNature'] = "";
                $tags = $folder->getTagsNature();

                foreach ($tags as $tag) {
                    $dataFolder['tagsNature'] .= $tag->getName();
                    if (next($tags)) {
                        $dataFolder['tagsNature'] .= ' / ';
                    }
                }

                $dataFolder['tagsTown'] = "";
                $tags = $folder->getTagsTown();

                foreach ($tags as $tag) {
                    $dataFolder['tagsTown'] .= $tag->getName();
                    if (next($tags)) {
                        $dataFolder['tagsTown'] .= ' / ';
                    }
                }

                $dataFolder['natinfs'] = "";
                $natinfs = $folder->getNatinfs();

                foreach ($natinfs as $natinf) {
                    $dataFolder['natinfs'] .= ("<b>" . $natinf->getNum() . "</b> - " . $natinf->getQualification() . ", ");
                    if (next($natinfs)) {
                        $dataFolder['natinfs'] .= ' <br> ';
                    }
                }
            }

            array_push($data, $dataFolder);
        }

        return new Response(json_encode($data), 200);
    }

    /**
     * Get all the MinutesClosedRegular
     * Used to display markers on the map
     */
    #[Route(path: '/getMinutesClosedRegular', name: 'lucca_get_minutesClosedRegular', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getMinutesClosedRegular(Request $request): Response
    {
        /** Coords of the border of the map visible */
        $minLat = $request->get('minLat');
        $minLon = $request->get('minLon');
        $maxLat = $request->get('maxLat');
        $maxLon = $request->get('maxLon');
        /** Use full for admin dashboard */
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        if ($request->get('adherent'))
            $adherent = $this->em->getRepository(Adherent::class)->find($request->get('adherent'));
        else
            $adherent = null;
        /** In order to use the same logic in all map get the date start and end only in the admin dashboard */
        if ($dateStart) {
            $minutes = $this->getMinutesBetweenDates($dateStart, $dateEnd, MinuteStory::STATUS_CLOSURE);
            /** Then is there is minutes use it to find the corresponding entities */
            if (count($minutes) < 1) {
                /** If the minute array is empty, return an empty folder array */
                $minutesClosedRegular = [];
            } else {
                $minutesClosedRegular = $this->em->getRepository(Minute::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon,
                    $adherent, [Closure::STATUS_REGULARIZED], SettingManager::get('setting.map.maxResults.name'), $minutes
                );
            }
        } else {
            $minutesClosedRegular = $this->em->getRepository(Minute::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon,
                $adherent, [Closure::STATUS_REGULARIZED], SettingManager::get('setting.map.maxResults.name')
            );
        }

        $data = [];
        foreach ($minutesClosedRegular as $minute) {
            $dataMinute['id'] = $minute->getId();
            $dataMinute['num'] = $minute->getNum();
            $dataMinute['lat'] = floatval($minute->getPlot()->getLatitude());
            $dataMinute['lon'] = floatval($minute->getPlot()->getLongitude());
            $dataMinute['address'] = $minute->getPlot()->getFullAddress();
            $dataMinute['agent'] = $minute->getAgent()->getOfficialName();

            array_push($data, $dataMinute);
        }

        return new Response(json_encode($data), 200);
    }

    /**
     * Get all the MinutesClosedOther
     * Used to display markers on the map
     */
    #[Route(path: '/getMinutesClosedOther', name: 'lucca_get_minutesClosedOther', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getMinutesClosedOther(Request $request): Response
    {
        /** Coords of the border of the map visible */
        $minLat = $request->get('minLat');
        $minLon = $request->get('minLon');
        $maxLat = $request->get('maxLat');
        $maxLon = $request->get('maxLon');
        /** Use full for admin dashboard */
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        if ($request->get('adherent'))
            $adherent = $this->em->getRepository(Adherent::class)->find($request->get('adherent'));
        else
            $adherent = null;

        /** In order to use the same logic in all map get the date start and end only in the admin dashboard */
        if ($dateStart) {
            $minutes = $this->getMinutesBetweenDates($dateStart, $dateEnd, MinuteStory::STATUS_CLOSURE);

            /** Then is there is minutes use it to find the corresponding entities */
            if (count($minutes) < 1) {
                /** If the minute array is empty, return an empty folder array */
                $minutesClosedOther = [];
            } else {
                $minutesClosedOther = $this->em->getRepository(Minute::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon,
                    $adherent, [Closure::STATUS_EXEC_OFFICE, Closure::STATUS_OTHER, Closure::STATUS_RELAXED], SettingManager::get('setting.map.maxResults.name', $minutes)
                );
            }
        } else {
            $minutesClosedOther = $this->em->getRepository(Minute::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon,
                $adherent, [Closure::STATUS_EXEC_OFFICE, Closure::STATUS_OTHER, Closure::STATUS_RELAXED], SettingManager::get('setting.map.maxResults.name')
            );
        }

        $data = [];

        /** @var Minute $minute */
        foreach ($minutesClosedOther as $minute) {
            $dataMinute['id'] = $minute->getId();
            $dataMinute['num'] = $minute->getNum();
            $dataMinute['lat'] = floatval($minute->getPlot()->getLatitude());
            $dataMinute['lon'] = floatval($minute->getPlot()->getLongitude());
            $dataMinute['address'] = $minute->getPlot()->getFullAddress();

            $dataMinute['agent'] = "";
            if ($minute->getAgent() instanceof Agent) {
                $dataMinute['agent'] = $minute->getAgent()->getOfficialName();
            }

            array_push($data, $dataMinute);
        }

        return new Response(json_encode($data), 200);
    }

    /**
     * Get all the Updatings
     * Used to display markers on the map
     */
    #[Route(path: '/getUpdatings', name: 'lucca_get_updatings', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getUpdatings(Request $request): Response
    {
        /** Coords of the border of the map visible */
        $minLat = $request->get('minLat');
        $minLon = $request->get('minLon');
        $maxLat = $request->get('maxLat');
        $maxLon = $request->get('maxLon');
        /** Use full for admin dashboard */
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        if ($request->get('adherent'))
            $adherent = $this->em->getRepository(Adherent::class)->find($request->get('adherent'));
        else
            $adherent = null;

        /** In order to use the same logic in all map get the date start and end only in the admin dashboard */
        if ($dateStart) {
            $minutes = $this->getMinutesBetweenDates($dateStart, $dateEnd, MinuteStory::STATUS_UPDATING);

            /** Then is there is minutes use it to find the corresponding entities */
            if (count($minutes) < 1) {
                /** If the minute array is empty, return an empty folder array */
                $updatings = [];
            } else {
                $updatings = $this->em->getRepository(Updating::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent, SettingManager::get('setting.map.maxResults.name', $minutes));
            }
        } else {
            $updatings = $this->em->getRepository(Updating::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent, SettingManager::get('setting.map.maxResults.name'));
        }
        $data = [];

        foreach ($updatings as $updating) {
            $dataUpdating['id'] = $updating->getId();
            $dataUpdating['num'] = $updating->getNum();
            $dataUpdating['lat'] = floatval($updating->getMinute()->getPlot()->getLatitude());
            $dataUpdating['lon'] = floatval($updating->getMinute()->getPlot()->getLongitude());
            $dataUpdating['minuteNum'] = floatval($updating->getMinute()->getNum());
            $dataUpdating['minuteId'] = floatval($updating->getMinute()->getId());
            $dataUpdating['address'] = $updating->getMinute()->getPlot()->getFullAddress();
            $dataUpdating['agent'] = $updating->getMinute()->getAgent()->getOfficialName();

            array_push($data, $dataUpdating);
        }

        return new Response(json_encode($data), 200);
    }

    /**
     * Get all the MinutesSpotted
     * Used to display markers on the map
     */
    #[Route(path: '/getMinutesSpotted', name: 'lucca_get_minutesSpotted', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getMinutesSpotted(Request $request): Response
    {
        /** Coords of the border of the map visible */
        $minLat = $request->get('minLat');
        $minLon = $request->get('minLon');
        $maxLat = $request->get('maxLat');
        $maxLon = $request->get('maxLon');
        /** Use full for admin dashboard */
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        if ($request->get('adherent'))
            $adherent = $this->em->getRepository(Adherent::class)->find($request->get('adherent'));
        else
            $adherent = null;

        /** In order to use the same logic in all map get the date start and end only in the admin dashboard */
        if ($dateStart) {
            $minutes = $this->getMinutesBetweenDates($dateStart, $dateEnd, MinuteStory::STATUS_OPEN);

            /** Then is there is minutes use it to find the corresponding entities */
            if (count($minutes) < 1) {
                /** If the minute array is empty, return an empty folder array */
                $foldersOpen = [];
            } else {
                $foldersOpen = $this->em->getRepository(Minute::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent, null, null, $minutes);
            }
        } else {
            /** We can't use max result here because we don't get ony spotting */
            $foldersOpen = $this->em->getRepository(Minute::class)->findAllInArea($minLat, $maxLat, $minLon, $maxLon, $adherent);
        }

        $minutesSpotted = [];

        foreach ($foldersOpen as $ind => $minute) {
            if ($minute->getControls()->isEmpty() && $minute->getUpdatings()->isEmpty() && $minute->getDecisions()->isEmpty()) {
                array_push($minutesSpotted, $minute);
                unset($foldersOpen[array_search($minute, $foldersOpen)]);
            }

            /** We don't want to have more marker than maxResults given in settings  */
            if ($ind >= SettingManager::get('setting.map.maxResults.name'))
                break;
        }

        $data = [];

        /** @var Minute $minute */
        foreach ($minutesSpotted as $minute) {
            $dataMinute['id'] = $minute->getId();
            $dataMinute['num'] = $minute->getNum();
            $dataMinute['lat'] = floatval($minute->getPlot()->getLatitude());
            $dataMinute['lon'] = floatval($minute->getPlot()->getLongitude());
            $dataMinute['address'] = $minute->getPlot()->getFullAddress();
            $dataMinute['agent'] = $minute->getAgent()->getOfficialName();
            if ($minute->getDateComplaint()) {
                $dataMinute['dateComplaint'] = $minute->getDateComplaint()->format('d/m/Y');
            } else {
                $dataMinute['dateComplaint'] = null;
            }

            array_push($data, $dataMinute);
        }

        return new Response(json_encode($data), 200);
    }

    /**
     * This function is used only in this controller to get the minutes associated to a minute story
     * that is
     *     - the last of the minute
     *     - and have been updated between the 2 research dates
     */
    private function getMinutesBetweenDates($dateStart, $dateEnd, $status): array
    {
        /** If there is a dateStart it's mean we need to filter data depends on stories between 2 dates */
        $datetimeStart = (new \DateTime())->createFromFormat('d/m/Y', $dateStart);
        $datetimeEnd = (new \DateTime())->createFromFormat('d/m/Y', $dateEnd);
        /** Get all the stories between to date with the right status */
        $stories = $this->em->getRepository(MinuteStory::class)->findLast($datetimeStart, $datetimeEnd, [$status]);
        $minutes = [];
        /** For each story get the associated minutes */
        foreach ($stories as $story) {
            $minutes[] = $story->getMinute();
        }

        return $minutes;
    }
}
