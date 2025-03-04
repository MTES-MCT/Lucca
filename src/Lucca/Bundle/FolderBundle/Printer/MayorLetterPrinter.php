<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Printer;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Twig\Environment;
use Twig\Error\Error;

use Lucca\Bundle\SettingBundle\Manager\SettingManager;
use Lucca\Bundle\AdherentBundle\Finder\LogoFinder;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\MediaBundle\Entity\Media;

class MayorLetterPrinter
{
    /**
     * List of options => WkhtmlToPdf
     */
    private array $options;

    /**
     *  Get info from parameters
     */
    private $logoInHeader;

    public function __construct(
        private readonly Environment $twig,
        private readonly LogoFinder $logoFinder,
    )
    {
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
     */
    public function createMayorLetterOptions(Adherent $adherent): array
    {
        $header = null;
        $footer = null;
        if ($this->logoInHeader){
            /** Define Logo and set margin */
            $logo = $this->defineLogo($adherent);
            try {
                $header = $this->twig->render('@LuccaAdherentBundle/Adherent/Printing/header-pdf.html.twig', array(
                    'adherent' => $adherent, 'officialLogo' => $logo
                ));
            } catch (Error $twig_Error) {
                echo 'Twig_Error has been thrown - Header Folder ' . $twig_Error->getMessage();
            }

            $this->options['header-html'] = $header;
        }

        try {
            $footer = $this->twig->render('@LuccaThemeAngleBundle:Print:footer.html.twig');
        } catch (Error $twig_Error) {
            echo 'Twig_Error has been thrown - Footer Folder ' . $twig_Error->getMessage();
        }
        $this->options['footer-html'] = $footer;

        return $this->options;
    }

    /**
     * Define specific logo who was used
     * Increase Margin top if a logo is used
     */
    private function defineLogo(Adherent $p_adherent): ?Media
    {
        $logo = $this->logoFinder->findLogo($p_adherent);

        /** Increase margin-top */
        if ($logo) {
            $this->options['margin-top'] = 35;
        }

        return $logo;
    }

    public function getName(): string
    {
        return 'lucca.utils.printer.mayor.letter';
    }
}
