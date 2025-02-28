<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Printer;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Lucca\AdherentBundle\Entity\Adherent;
use Lucca\AdherentBundle\Finder\LogoFinder;
use Lucca\SettingBundle\Utils\SettingManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Twig\Error\Error;

/**
 * Class ControlPrinter
 *
 * @package Lucca\MinuteBundle\Printer
 * @author Terence <terence@numeric-wave.tech>
 */
class ControlPrinter
{
    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var LogoFinder
     */
    private $logoFinder;

    /**
     * List of options => WkhtmlToPdf
     */
    private $options;

    /**
     *  Get info from parameters
     */
    private $logoInHeader;

    /**
     * ControlPrinter constructor.
     * @param TwigEngine $templating
     * @param LogoFinder $logoFinder
     */
    public function __construct(TwigEngine $templating, LogoFinder $logoFinder)
    {
        $this->templating = $templating;
        $this->logoFinder = $logoFinder;
        $this->logoInHeader = SettingManager::get('setting.pdf.logoInHeader.name');

        $this->options = array(
            'margin-top' => 11, 'margin-right' => 11,
            'margin-bottom' => 11, 'margin-left' => 11,
        );
    }

    /**
     * Create a basic model Access Letter with Knp options
     *
     * @param Control $p_control
     * @return array
     */
    public function createAccessLetterOptions(Control $p_control)
    {
        /** Access Letter have a specific footer */
        $this->options['margin-bottom'] = 55;

        /** @var Adherent $adherent */
        $adherent = $p_control->getMinute()->getAdherent();

        /** Define Logo and set margin */
        $logo = $this->defineLogo($adherent);

        try {
            $header = $this->templating->render('LuccaAdherentBundle:Adherent/Printing:header-pdf.html.twig', array(
                'adherent' => $adherent, 'officialLogo' => $logo
            ));
        } catch (Error $twig_Error) {
            echo 'Twig_Error has been thrown - Header Access Letter ' . $twig_Error->getMessage();
        }

        try {
            $departement = SettingManager::get('setting.general.departement.name');
            if (SettingManager::get('setting.control.footer.name') == true)
                $footer = $this->templating->render('LuccaMinuteBundle:Control/Printing/Custom:footer-' . $departement . '.html.twig');
            else
                $footer = $this->templating->render('LuccaMinuteBundle:Control/Printing/Basic:footer.html.twig');
        } catch (Error $twig_Error) {
            echo 'Twig_Error has been thrown - Footer Access Letter ' . $twig_Error->getMessage();
        }

        $this->options['header-html'] = $header;
        $this->options['footer-html'] = $footer;

        return $this->options;
    }

    /**
     * Create a basic model to Convocation with Knp options
     *
     * @param Control $p_control
     * @return array
     */
    public function createConvocationLetterOptions(Control $p_control)
    {
        /** TODO The following code is only here as temporary fix */
        /** The issue was : old document didn't have a logo displayed after we implement the new
         *  graphic chart.
         *  To solve this issue and display the old header for old document we check if the creation date of the
         *  document is before the date of the update
         *  tHe issue appear only on edited documents
         *
         *  PLEASE : Remove this code when a good solution is created
         */
        /** Start of the temporary code */
        $dateUpdate = (new \DateTime())->createFromFormat('d/m/Y', '09/03/2021');
        $header = null;

        $isEdited = false;
        foreach ($p_control->getEditions() as $edition) {
            if ($edition->getConvocationEdited())
                $isEdited = true;
        }

        if ($this->logoInHeader || ($p_control->getCreatedAt() < $dateUpdate && $isEdited)) {
            /**************************** End of the temporary code *******************************************/

//        if ($this->logoInHeader == true) { /** Uncomment this code when you remove the previous code */
            /** @var Adherent $adherent */
            $adherent = $p_control->getMinute()->getAdherent();

            /** Define Logo and set margin */
            $logo = $this->defineLogo($adherent);

            try {
                $header = $this->templating->render('LuccaAdherentBundle:Adherent/Printing:header-pdf.html.twig', array(
                    'adherent' => $adherent, 'officialLogo' => $logo
                ));
            } catch (Error $twig_Error) {
                echo 'Twig_Error has been thrown - Header Access Letter ' . $twig_Error->getMessage();
            }

            $this->options['header-html'] = $header;

            return $this->options;
        } else {
            return array();
        }
    }

    /**
     * Define specific logo who was used
     * Increase Margin top if a logo is used
     *
     * @param Adherent $p_adherent
     * @return \Lucca\MediaBundle\Entity\Media|null
     */
    public function defineLogo(Adherent $p_adherent)
    {
        $logo = $this->logoFinder->findLogo($p_adherent);

        /** Increase margin-top */
        if ($logo)
            $this->options['margin-top'] = 35;

        return $logo;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.utils.printer.control';
    }
}
