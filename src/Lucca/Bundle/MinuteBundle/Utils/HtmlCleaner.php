<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Utils;

class HtmlCleaner
{
    /**
     * This function will remove specific regex given from the html given
     */
    private function removeRegexFromHtml($p_regex, $p_html): array|string|null
    {
        $match = null;
        /** While the asking regex is find in html remove it */
        while (preg_match($p_regex, $p_html, $match)) {
            /** Use match[0] because
             * match[0] contain the complete result and
             * match[1] contain result only of regex between parenthesis
             */
            $p_html = str_replace($match[0], "", $p_html);
        }

        return $p_html;
    }

    /**
     * Remove all font family from html
     */
    public function removeAllFonts(?string $html): ?string
    {

        if($html === null){
            return null;
        }

        // Array of regex patterns
        $patterns = [
            '/font-family\s*:\s*[^;"]+;?\s*/i', // Remove all font-family declarations
            '/font-size\s*:\s*[^;"]+;?\s*/i',   // Remove all font-size declarations
            '/<span>\s*<\/span>/i',             // Remove empty <span> tags without any attributes
            '/style="\s*"/i'                    // Remove empty style attributes
        ];

        // Apply all regex patterns
        foreach ($patterns as $pattern) {
            $html = preg_replace($pattern, '', $html);
        }

        // Optional: remove redundant spaces left after cleaning
        $html = preg_replace('/\s+/', ' ', $html);

        return trim($html);
    }

    /***************************** Functions to clean specific entity *****************************************************/

    /**
     * Clean all html field in Folder
     * -> ascertainment + details + violation
     * Edition -> FolderVersion
     */
    public function cleanHtmlFolder($p_folder): void
    {
        // Clean Folder
        if ($p_folder->getAscertainment()) {
            $p_folder->setAscertainment($this->removeAllFonts($p_folder->getAscertainment()));
        }

        if ($p_folder->getViolation()) {
            $p_folder->setViolation($this->removeAllFonts($p_folder->getViolation()));
        }

        if ($p_folder->getDetails()) {
            $p_folder->setDetails($this->removeAllFonts($p_folder->getDetails()));
        }

        /** Clean html of edition if exist */
        $editionFolder = $p_folder->getEdition();
        // Clean Folder Edition
        if ($editionFolder && $editionFolder->getFolderEdited()) {
            $editionFolder->setFolderVersion($this->removeAllFonts($editionFolder->getFolderVersion()));
        }
    }

    /**
     * Clean all html field in Control associated to Folder
     *  Edition -> letterConvocation + letterAccess
     */
    public function cleanHtmlControl($p_control): void
    {
        /** Clean html of control edition if exist */
        // Clean Control Edition
        if (count($p_control->getEditions()) > 0) {
            foreach ($p_control->getEditions() as $p_controlEdition) {
                if (!$p_controlEdition->getAccessEdited()) {
                    /** Call service to clean all html of this step from useless fonts */
                    $p_controlEdition->setLetterAccess($this->removeAllFonts($p_controlEdition->getLetterAccess()));
                }
                if (!$p_controlEdition->getConvocationEdited()) {
                    /** Call service to clean all html of this step from useless fonts */
                    $p_controlEdition->setLetterConvocation($this->removeAllFonts($p_controlEdition->getLetterConvocation()));
                }
            }
        }
    }

    /**
     * Clean all html field in Courier associated to Folder
     *  Edition -> letterJudicial + letterDDTM
     *  Human Edition -> letterOffender
     */
    public function cleanHtmlCourier($p_courier): void
    {
        // Clean Courier
        $p_courier->setContext($this->removeAllFonts($p_courier->getContext()));

        /** Clean html of courier edition if exist */
        $editionCourier = $p_courier->getEdition();
        // Clean Edition
        if ($editionCourier) {
            if ($editionCourier->getDdtmEdited()) {
                $editionCourier->setLetterDdtm($this->removeAllFonts($editionCourier->getLetterDdtm()));
            }
            if ($editionCourier->getJudicialEdited()) {
                $editionCourier->setLetterJudicial($this->removeAllFonts($editionCourier->getLetterJudicial()));
            }
        }

        /** Clean html of courier -> humans editions if exist */
        // Clean Human editions
        if (count($p_courier->getHumansEditions()) > 0) {
            foreach ($p_courier->getHumansEditions() as $courierHumanEdition) {
                if ($courierHumanEdition->getLetterOffenderEdited()) {
                    $courierHumanEdition->setLetterOffender($this->removeAllFonts($courierHumanEdition->getLetterOffender()));
                }
            }
        }
    }

    /**
     * Clean all html field in Decision and linked entity
     * Decision -> dataEurope
     * Expulsion -> comment
     * Demolition -> comment
     * Contradictory -> answer
     * tribunalCommission -> restitution
     * appealCommission -> restitution
     * cassationComission -> restitution
     */
    public function cleanHtmlDecision($p_decision): void
    {
        // Clean Decision
        $p_decision->setDataEurope($this->removeAllFonts($p_decision->getDataEurope()));

        // Clean Expulsion
        $expulsion = $p_decision->getExpulsion();
        if ($expulsion) {
            $expulsion->setComment($this->removeAllFonts($expulsion->getComment()));
        }

        // Clean Demolition
        $demolition = $p_decision->getDemolition();
        if ($demolition) {
            $demolition->setComment($this->removeAllFonts($demolition->getComment()));
        }

        // Clean all Contradictory
        $contradictories = $p_decision->getContradictories();
        if (count($contradictories) > 0) {
            foreach ($contradictories as $contradictory) {
                $contradictory->setAnswer($this->removeAllFonts($contradictory->getAnswer()));
            }
        }

        // Clean tribunalCommission
        $tribunalCommission = $p_decision->getTribunalCommission();
        if ($tribunalCommission) {
            $tribunalCommission->setRestitution($this->removeAllFonts($tribunalCommission->getRestitution()));
        }


        // Clean appealCommission
        $appealCommission = $p_decision->getAppealCommission();
        if ($appealCommission) {
            $appealCommission->setRestitution($this->removeAllFonts($appealCommission->getRestitution()));
        }


        // Clean cassationComission
        $cassationComission = $p_decision->getCassationComission();
        if ($cassationComission) {
            $cassationComission->setRestitution($this->removeAllFonts($cassationComission->getRestitution()));
        }
    }
}
