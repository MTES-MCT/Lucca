<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Manager;

use DateTime, DateInterval, DatePeriod;
use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\DecisionBundle\Entity\Decision;
use Lucca\Bundle\FolderBundle\Entity\{Courier, Folder};
use Lucca\Bundle\MinuteBundle\Entity\{Closure, Control, Minute, MinuteStory, Updating};

readonly class MinuteStoryManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private AdherentFinder $adherentFinder,
    )
    {
    }

    /**
     * Check and update Minute status
     *
     * This function is called in each controller that add object to the minute
     *
     * @throws ORMException
     */
    public function manage(Minute $minute): void
    {
        /** init steps */
        $controls = $this->em->getRepository(Control::class)->findByMinute($minute);
        $folders = $this->em->getRepository(Folder::class)->findSmallFolderByMinute($minute);
        $couriers = array();
        $updates = $this->em->getRepository(Updating::class)->findByMinute($minute);
        $decisions = $this->em->getRepository(Decision::class)->findByMinute($minute);
        $closure = $this->em->getRepository(Closure::class)->findByMinute($minute);

        $status = 1;

        /** Check if controls exist for this minute */
        if ($controls != null && count($controls) > 0) {
            $status = 2;
        }

        /** Check if folder exist for this minute */
        if ($folders != null && count($folders) > 0) {
            foreach ($folders as $folder) {
                $couriers = $this->em->getRepository(Courier::class)->findCouriersByFolder($folder);
            }
            $status = 3;
        }

        /** Get the last correct minute in order to be able to save or not a new minuteStory */
        $lastMinuteStory = $this->em->getRepository(MinuteStory::class)->findLastByMinute($minute);

        /** Depend on the objects linked to the minute set the status */
        /** The status will always be the most close to the closure */
        /** Store the new status as a integer in order to compare to the old one */
        $newStatus = -1;

        /** Init status minute with the current status in order to avoid errors */
        $statusMinute = $minute->getStatus();
        switch (true) {
            case ($closure == !null):
                $status = MinuteStory::STATUS_CLOSURE;
                $statusMinute = Minute::STATUS_CLOSURE;
                $newStatus = 7;
                break;
            case ($decisions != null && count($decisions) > 0):
                $status = MinuteStory::STATUS_DECISION;
                $statusMinute = Minute::STATUS_DECISION;
                $newStatus = 6;
                break;
            case ($updates != null && count($updates) > 0):
                $status = MinuteStory::STATUS_UPDATING;
                $statusMinute = Minute::STATUS_UPDATING;
                $newStatus = 5;
                break;
            case ($couriers != null && count($couriers) > 0):
                $status = MinuteStory::STATUS_COURIER;
                $statusMinute = Minute::STATUS_COURIER;
                $newStatus = 4;
                break;
            case ($status === 3):
                $status = MinuteStory::STATUS_FOLDER;
                $statusMinute = Minute::STATUS_FOLDER;
                $newStatus = 3;
                break;
            case ($status === 2):
                $status = MinuteStory::STATUS_CONTROL;
                $statusMinute = Minute::STATUS_CONTROL;
                $newStatus = 2;
                break;
            case ($status === 1):
                $status = MinuteStory::STATUS_OPEN;
                $statusMinute = Minute::STATUS_OPEN;
                $newStatus = 1;
                break;
        }

        /** Store the old status as a integer in order to compare to the new one */
        $oldStatus = -1;
        if ($lastMinuteStory != null) {
            switch ($lastMinuteStory[0]->getStatus()) {
                case (MinuteStory::STATUS_CLOSURE):
                    $oldStatus = 7;
                    break;
                case (MinuteStory::STATUS_DECISION):
                    $oldStatus = 6;
                    break;
                case (MinuteStory::STATUS_UPDATING):
                    $oldStatus = 5;
                    break;
                case (MinuteStory::STATUS_COURIER):
                    $oldStatus = 4;
                    break;
                case (MinuteStory::STATUS_FOLDER):
                    $oldStatus = 3;
                    break;
                case (MinuteStory::STATUS_CONTROL):
                    $oldStatus = 2;
                    break;
                case (MinuteStory::STATUS_OPEN):
                    $oldStatus = 1;
                    break;
            }
        }

        /** Check if there is a difference because only the more closest status to closure is saved */
        if ($lastMinuteStory == null || $oldStatus != $newStatus) {
            $minuteStory = new MinuteStory();
            $minuteStory->setStatus($status);
            $minuteStory->setMinute($minute);

            /** Set the update date to now */
            $now = new DateTime();
            $minuteStory->setDateUpdate($now);

            /** Set the adherent who as update the status */
            $minuteStory->setUpdatingBy($this->adherentFinder->whoAmI());
            $this->em->persist($minuteStory);

            /** We need to also, update the status stored in the minute */
            $minute->setStatus($statusMinute);
            $this->em->persist($minute);
        }
    }

    /**
     * Keep only the last stories for each minute
     *
     * @throws Exception
     */
    public function keepLastStories(array $minutesStories, $dateStart, $dateEnd): array
    {
        /** Create array that will be useful */
        $result = [];
        /** Create a sub array stories in order to use it easily in twig */
        $result['stories'] = [];
        $temp = [];

        /** Group stories by minute */
        /** @var MinuteStory $latestStory */
        foreach ($minutesStories as $minuteStory) {
            $temp[$minuteStory->getMinute()->getId()][] = $minuteStory;
        }

        foreach ($temp as $minuteStories) {
            $latestStory = null;
            /** Go across each $p_minutesStories in order to keep only the last story of a minute and order it in specific array */
            foreach ($minuteStories as $story) {
                if ($latestStory == null || $latestStory->getDateUpdate() < $story->getDateUpdate()) {
                    $latestStory = $story;
                }
            }
            /** Sort stories in result array depend on the status */
            if (!array_key_exists($latestStory->getStatus(), $result['stories'])) {
                $result['stories'][$latestStory->getStatus()] = [];
            }

            /** We need to order the sub array by month, we only count how much stories there are we don't keep details */
            if (!array_key_exists($latestStory->getDateUpdate()->format('Y-m'), $result['stories'][$latestStory->getStatus()])) {
                $result['stories'][$latestStory->getStatus()][$latestStory->getDateUpdate()->format('Y-m')] = 0;
            }

            $result['stories'][$latestStory->getStatus()][$latestStory->getDateUpdate()->format('Y-m')] += 1;
        }


        /** We need to keep the list of month in order to display it, use the dates of the filter to get the full list */
        $start = (clone($dateStart));
        $end = (clone($dateEnd));
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $arrayMonth = [];

        foreach ($period as $item) {
            array_push($arrayMonth, $item->format('Y-m'));
        }
        $result['month'] = $arrayMonth;

        /** We need to init the month missing for each status */
        foreach ($arrayMonth as $month) {
            foreach ($result['stories'] as $key => $item) {
                if (!array_key_exists($month, $item)) {
                    $result['stories'][$key][$month] = 0;
                }
                /** We need to sort the array in order to have  */
                ksort($result['stories'][$key]);
            }
        }

        /** Store the number of closed minute */
        $result['totalClosed'] = 0;
        if (array_key_exists(MinuteStory::STATUS_CLOSURE, $result['stories'])) {
            foreach ($result['stories'][MinuteStory::STATUS_CLOSURE] as $monthItem) {
                $result['totalClosed'] += $monthItem;
            }
        }

        /** Store the number of minute that are not closed */
        $result['totalUnclosed'] = 0;
        foreach ($result['stories'] as $key => $item) {
            if ($key != MinuteStory::STATUS_CLOSURE) {
                foreach ($item as $monthItem) {
                    $result['totalUnclosed'] += $monthItem;
                }
            }
        }

        /** Return a table like :
         *  "stories" => array(
         *      "choice.statusMinute.courier" => array of month containing number of stories
         *      "choice.statusMinute.updating" => array of month containing number of stories
         *      "choice.statusMinute.closure" => array of month containing number of stories
         *      "choice.statusMinute.folder" => array of month containing number of stories
         *      "choice.statusMinute.control" => array of month containing number of stories
         *      "choice.statusMinute.open" => array of month containing number of stories
         *  )
         *  "totalClosed" => 2
         *  "totalUnclosed" => 82
         */
        return $result;
    }

    /**
     * Create an array with the diagram of reason of closure
     */
    public function manageClosureData($dateStart, $dateEnd, $p_minutes = null): array
    {
        /** Get all the stories that are in the range of date and with the status close */
        $minutesStoriesClosure = $this->em->getRepository(MinuteStory::class)->findClosureBetween($dateStart, $dateEnd, $p_minutes);

        $result = array();
        /** Construct an array with key (from closure entity) :
         *  -'choice.status.regularized'
         *  -'choice.status.exec_office'
         *  -'choice.status.relaxed'
         *  -'choice.status.other'
         */
        /** @var MinuteStory $story */
        foreach ($minutesStoriesClosure as $story) {
            /** Be careful if the key is not yet in the array we need to initialize at 0 */
            if ($story->getMinute()->getClosure()) {
                $statusClosure = $story->getMinute()->getClosure()->getStatus();
                if (!array_key_exists($statusClosure, $result)) {
                    $result[$statusClosure] = array();
                    $result[$statusClosure]['count'] = 0;

                    switch ($statusClosure) {
                        case 'choice.status.regularized' :
                            $result[$statusClosure]['color'] = "#23c054";
                            break;
                        case 'choice.status.exec_office' :
                            $result[$statusClosure]['color'] = "#80cb8e";
                            break;
                        case 'choice.status.relaxed' :
                            $result[$statusClosure]['color'] = "#1d8d3d";
                            break;
                        case 'choice.status.other' :
                            $result[$statusClosure]['color'] = "#868686";
                            break;
                        default:
                            $result[$statusClosure]['color'] = "#FFF";
                            break;
                    }
                }
                /** Then we can increase the value of this reason of closure */
                $result[$statusClosure]['count'] += 1;
            }
        }

        return $result;
    }
}
