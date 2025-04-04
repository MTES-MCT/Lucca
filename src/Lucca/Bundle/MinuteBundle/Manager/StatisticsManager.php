<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Manager;

use DateInterval, DatePeriod;

class StatisticsManager
{

    /**
     * Create array useful in stats
     */
    public function createOverallArray($dateStart, $dateEnd, array $folders, array $controls, array $decisions): array
    {
        /** Create array that will be useful */
        $result = [];

        /** We need to keep the list of month in order to display it, use the dates of the filter to get the full list */
        $start = (clone($dateStart));
        $end = (clone($dateEnd));
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $arrayMonth = [];

        /** Create an array containing all the months */
        foreach ($period as $item) {
            array_push($arrayMonth, $item->format('Y-m'));
        }

        /** Add all month in the final array and init counters to 0 */
        foreach ($arrayMonth as $month) {
            $result[$month] = [];
            $result[$month]['folder'] = 0;
            $result[$month]['control'] = 0;
            $result[$month]['decision'] = 0;
            $result[$month]['ascertainment'] = 0;
            /** We need to sort the array in order to order month  */
            ksort($result);
        }

        /** Add number of folder in result array */
        foreach ($folders as $folder) {
            $month = $folder->getMinute()->getDateOpening()->format('Y-m');
            if (array_key_exists($month, $result)) {
                if ($folder->getNatinfs() !== null && count($folder->getNatinfs()) === 0) {
                    $result[$month]['ascertainment'] += 1;
                }
                else {
                    $result[$month]['folder'] += 1;
                }
            }
        }

        /** Add number of control in result array */
        foreach ($controls as $control) {
            $month = $control->getMinute()->getDateOpening()->format('Y-m');
            if (array_key_exists($month, $result)) {
                $result[$month]['control'] += 1;
            }
        }

        /** Add number of decision in result array */
        foreach ($decisions as $decision) {
            $month = $decision->getMinute()->getDateOpening()->format('Y-m');
            if (array_key_exists($month, $result)) {
                $result[$month]['decision'] += 1;
            }
        }

        /** Return a table like :
         *  "2021-01" => array(
         * $result[$month]['decision'] += 1;
         *      "folder" => 4
         *      "control" => 10
         *      "decision" => 7
         *  )
         *  "2021-02" => array(
         *      "folder" => 1
         *      "control" => 3
         *  )
         */
        return $result;
    }

    /**
     * Create array containing number of closed and unclosed minutes
     */
    public function createCloseUnclosedArray($minutes): array
    {
        /** Init array and counters */
        $res = [];
        $res['unclosed'] = 0;
        $res['closed'] = 0;

        /** Update counter depend on if there is a closure linked to the minute */
        foreach ($minutes as $minute) {
            if ($minute->getClosure() == null) {
                $res['unclosed'] += 1;
            } else {
                $res['closed'] += 1;
            }
        }

        return $res;
    }

    /**
     * Create array useful in stats minutes
     * This function is used to create array containing data bout control and folder of each minute
     */
    public function createMinuteDataArray($minutes): array
    {
        /** init data array */
        $data = [];

        /** Go across each minutes */
        foreach ($minutes as $minute) {
            /** For each minute init needed array and counters */
            $data[$minute->getNum()] = [];
            $data[$minute->getNum()]['controlState'] = [];
            $data[$minute->getNum()]['folderNature'] = [];
            $data[$minute->getNum()]['folderDateClosure'] = [];
            $data[$minute->getNum()]['folderNatinf'] = [];
            $data[$minute->getNum()]['countFolders'] = 0;

            /** Check all controls */
            foreach ($minute->getControls() as $control) {
                /** If the state of the current control is not yet in data array for the current minute save it */
                if (!in_array($control->getStateControl(), $data[$minute->getNum()]['controlState'])) {
                    $data[$minute->getNum()]['controlState'][] = $control->getStateControl();
                }

                /** If there is a folder linked to the minute */
                if ($control->getFolder()) {
                    $folder = $control->getFolder();
                    $data[$minute->getNum()]['countFolders'] += 1;

                    /** If the nature of the current folder is not yet in data array for the current minute save it */
                    if (!in_array($folder->getNature(), $data[$minute->getNum()]['folderNature'])) {
                        $data[$minute->getNum()]['folderNature'][] = $folder->getNature();
                    }

                    /** If the closure date of the current folder is not yet in data array for the current minute save it */
                    if ($folder->getDateClosure()) {
                        if (!in_array($folder->getDateClosure()->format('d/m/Y'), $data[$minute->getNum()]['folderDateClosure'])) {
                            $data[$minute->getNum()]['folderDateClosure'][] = $folder->getDateClosure()->format('d/m/Y');
                        }
                    }

                    /** If the natinfs of the current folder is not yet in data array for the current minute save it */
                    foreach ($folder->getNatinfs() as $natinf) {
                        if (!array_key_exists($natinf->getNum(), $data[$minute->getNum()]['folderNatinf'])) {
                            $natinfData['num'] = $natinf->getNum();
                            $natinfData['qualification'] = $natinf->getQualification();
                            $data[$minute->getNum()]['folderNatinf'][$natinf->getNum()] = $natinfData;
                        }
                    }
                }
            }
        }

        /** Return a table like :
         *  "13-DDTM34-001" => [
         *    "controlState" => [
         *        0 => "choice.state.outside"
         *    ]
         *    "folderNature" => [
         *        0 => "choice.nature.obstacle"
         *        1 => "choice.nature.hut"
         *    ]
         *    "folderDateClosure" => [
         *        0 => "07/09/2021"
         *    ]
         *    "folderNatinf" => [
         *        0 => 33058
         *        1 => 341
         *        2 => 23018
         *     ]
         *    "countFolders" => 5
         *  ]
         */
        return $data;
    }

    /**
     * Depend on filters return if filters data are correct or not
     */
    public function checkDatesFilters($filters): bool
    {

        /** Check if the 2 date filters date are filling */
        if ($filters['dateStart'] != null && $filters['dateEnd'] == null) {
            return false;
        }
        if ($filters['dateStart'] == null && $filters['dateEnd'] != null) {
            return false;
        }

        /** Check if the 2 date closure filters date are filling */
        if ($filters['dateStartClosure'] != null && $filters['dateEndClosure'] == null) {
            return false;
        }
        if ($filters['dateStartClosure'] == null && $filters['dateEndClosure'] != null) {
            return false;
        }

        return true;
    }

    /**
     * Count the number of folder without natinfs
     */
    public function countFolderWithoutNatinfs($folders): int
    {
        $count = 0;

        foreach ($folders as $folder) {
            if ($folder->getNatinfs() !== null && count($folder->getNatinfs()) === 0) {
                $count += 1;
            }
        }

        return $count;
    }
}
