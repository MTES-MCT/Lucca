<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\MinuteBundle\Utils;

/**
 * Class MinuteManager
 *
 * @package Lucca\MinuteBundle\Utils
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class StatisticsManager
{
    /**
     * StatisticsManager constructor
     *
     */
    public function __construct()
    {
    }

    /**
     * Create array useful in stats
     *
     * @param $p_dateStart
     * @param $p_dateEnd
     * @param array $p_folders
     * @param array $p_controls
     * @param array $p_decisions
     * @return array
     */
    public function createOverallArray($p_dateStart, $p_dateEnd, array $p_folders, array $p_controls, array $p_decisions)
    {
        /** Create array that will be useful */
        $result = array();

        /** We need to keep the list of month in order to display it, use the dates of the filter to get the full list */
        $start = (clone($p_dateStart));
        $end = (clone($p_dateEnd));
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);
        $arrayMonth = array();

        /** Create an array containing all the months */
        foreach ($period as $item) {
            array_push($arrayMonth, $item->format('Y-m'));
        }

        /** Add all month in the final array and init counters to 0 */
        foreach ($arrayMonth as $month) {
            $result[$month] = array();
            $result[$month]['folder'] = 0;
            $result[$month]['control'] = 0;
            $result[$month]['decision'] = 0;
            $result[$month]['ascertainment'] = 0;
            /** We need to sort the array in order to order month  */
            ksort($result);
        }

        /** Add number of folder in result array */
        foreach ($p_folders as $folder) {
            $month = $folder->getMinute()->getDateOpening()->format('Y-m');
            if (array_key_exists($month, $result)) {
                if ($folder->getNatinfs() !== null && count($folder->getNatinfs()) === 0)
                    $result[$month]['ascertainment'] += 1;
                else
                    $result[$month]['folder'] += 1;
            }
        }

        /** Add number of control in result array */
        foreach ($p_controls as $control) {
            $month = $control->getMinute()->getDateOpening()->format('Y-m');
            if (array_key_exists($month, $result))
                $result[$month]['control'] += 1;
        }

        /** Add number of decision in result array */
        foreach ($p_decisions as $decision) {
            $month = $decision->getMinute()->getDateOpening()->format('Y-m');
            if (array_key_exists($month, $result))
                $result[$month]['decision'] += 1;
        }

        /** Return a table like :
         *  "2021-01" => array(
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
     *
     * @param $p_minutes
     * @return array
     */
    public function createCloseUnclosedArray($p_minutes)
    {
        /** Init array and counters */
        $res = array();
        $res['unclosed'] = 0;
        $res['closed'] = 0;

        /** Update counter depend on if there is a closure linked to the minute */
        foreach ($p_minutes as $minute) {
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
     *
     * @param $p_minutes
     * @return array
     */
    public function createMinuteDataArray($p_minutes)
    {
        /** init data array */
        $data = array();

        /** Go across each minutes */
        foreach ($p_minutes as $minute) {
            /** For each minute init needed array and counters */
            $data[$minute->getNum()] = array();
            $data[$minute->getNum()]['controlState'] = array();
            $data[$minute->getNum()]['folderNature'] = array();
            $data[$minute->getNum()]['folderDateClosure'] = array();
            $data[$minute->getNum()]['folderNatinf'] = array();
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
     *Depend on filters return if filters data are correct or not
     *
     * @param $p_filters
     * @return bool
     */
    public function checkDatesFilters($p_filters)
    {

        /** Check if the 2 date filters date are filling */
        if ($p_filters['dateStart'] != null && $p_filters['dateEnd'] == null) {
            return false;
        }
        if ($p_filters['dateStart'] == null && $p_filters['dateEnd'] != null) {
            return false;
        }

        /** Check if the 2 date closure filters date are filling */
        if ($p_filters['dateStartClosure'] != null && $p_filters['dateEndClosure'] == null) {
            return false;
        }
        if ($p_filters['dateStartClosure'] == null && $p_filters['dateEndClosure'] != null) {
            return false;
        }

        return true;
    }

    /**
     * Count the number of folder without natinfs
     *
     * @param $p_folders
     * @return int
     */
    public function countFolderWithoutNatinfs($p_folders)
    {
        $count = 0;
        foreach ($p_folders as $folder) {
            if ($folder->getNatinfs() !== null && count($folder->getNatinfs()) === 0) {
                $count += 1;
            }
        }
        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.minute_statistics';
    }
}
