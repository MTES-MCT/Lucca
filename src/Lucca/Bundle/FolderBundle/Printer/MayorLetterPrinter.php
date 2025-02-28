<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Printer;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Twig\Error\Error;

use Lucca\SettingBundle\Utils\SettingManager;
use Lucca\AdherentBundle\Finder\LogoFinder;
use Lucca\AdherentBundle\Entity\Adherent;
use Lucca\MediaBundle\Entity\Media;

/**
 * Class MayorLetterPrinter
 *
 * @package Lucca\MinuteBundle\Printer
 * @author Lisa <lisa.alvarez@numeric-wave.eu>
 */
class MayorLetterPrinter
{
    /**
     * @var TwigEngine
     */
    private TwigEngine $templating;

    /**
     * @var LogoFinder
     */
    private LogoFinder $logoFinder;

    /**
     * List of options => WkhtmlToPdf
     */
    private array $options;

    /**
     *  Get info from parameters
     */
    private $logoInHeader;

    /**
     * FolderPrinter constructor
     *
     * @param TwigEngine $templating
     * @param LogoFinder $logoFinder
     */
    public function __construct(TwigEngine $templating, LogoFinder $logoFinder)
    {
        $this->templating = $templating;
        $this->logoFinder = $logoFinder;
        $this->logoInHeader = SettingManager::get('setting.pdf.logoInHeader.name');

        /** Define basic options of this document */
        $this->options = array(
            'margin-top' => 15, 'margin-right' => 15,
            'margin-bottom' => 25, 'margin-left' => 15,
            'footer-spacing' => 10
        );
    }

    /**
     * Create a basic model Access Letter with Knp options
     *
     * @param Adherent $adherent
     * @return array
     */
    public function createMayorLetterOptions(Adherent $adherent)
    {
        $header = null;
        $footer = null;
        if ($this->logoInHeader){
            /** Define Logo and set margin */
            $logo = $this->defineLogo($adherent);
            try {
                $header = $this->templating->render('LuccaAdherentBundle:Adherent/Printing:header-pdf.html.twig', array(
                    'adherent' => $adherent, 'officialLogo' => $logo
                ));
            } catch (Error $twig_Error) {
                echo 'Twig_Error has been thrown - Header Folder ' . $twig_Error->getMessage();
            }

            $this->options['header-html'] = $header;
        }

        try {
            $footer = $this->templating->render('LuccaThemeAngleBundle:Print:footer.html.twig');
        } catch (Error $twig_Error) {
            echo 'Twig_Error has been thrown - Footer Folder ' . $twig_Error->getMessage();
        }
        $this->options['footer-html'] = $footer;

        return $this->options;

    }

    /**
     * Define specific logo who was used
     * Increase Margin top if a logo is used
     *
     * @param Adherent $p_adherent
     * @return Media|null
     */
    private function defineLogo(Adherent $p_adherent)
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
        return 'lucca.utils.printer.mayor.letter';
    }
}
