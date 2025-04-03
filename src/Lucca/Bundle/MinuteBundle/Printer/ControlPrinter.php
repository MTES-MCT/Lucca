<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Printer;

use DateTime;
use Lucca\Bundle\MediaBundle\Entity\Media;
use Twig\Environment;
use Twig\Error\Error;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Finder\LogoFinder;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

class ControlPrinter
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

        $this->options = [
            'margin-top' => 11, 'margin-right' => 11,
            'margin-bottom' => 11, 'margin-left' => 11,
        ];
    }

    /**
     * Create a basic model Access Letter with Knp options
     */
    public function createAccessLetterOptions(Control $control): array
    {
        /** Access Letter have a specific footer */
        $this->options['margin-bottom'] = 55;

        $adherent = $control->getMinute()->getAdherent();

        /** Define Logo and set margin */
        $logo = $this->defineLogo($adherent);

        try {
            $header = $this->twig->render('@LuccaAdherent/Adherent/Printing/header-pdf.html.twig', [
                'adherent' => $adherent, 'officialLogo' => $logo
            ]);
        } catch (Error $twig_Error) {
            echo 'Twig_Error has been thrown - Header Access Letter ' . $twig_Error->getMessage();
        }

        try {
            $departement = SettingManager::get('setting.general.departement.name');
            if (SettingManager::get('setting.control.footer.name') == true) {
                $footer = $this->twig->render('LuccaMinute/Control/Printing/Custom:footer-' . $departement . '.html.twig');
            } else {
                $footer = $this->twig->render('@LuccaMinute/Control/Printing/Basic/footer.html.twig');
            }
        } catch (Error $twig_Error) {
            echo 'Twig_Error has been thrown - Footer Access Letter ' . $twig_Error->getMessage();
        }

        $this->options['header-html'] = $header;
        $this->options['footer-html'] = $footer;

        return $this->options;
    }

    /**
     * Create a basic model to Convocation with Knp options
     */
    public function createConvocationLetterOptions(Control $control): array
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
        $dateUpdate = (new DateTime())->createFromFormat('d/m/Y', '09/03/2021');
        $header = null;

        $isEdited = false;
        foreach ($control->getEditions() as $edition) {
            if ($edition->getConvocationEdited()) {
                $isEdited = true;
            }
        }

        if ($this->logoInHeader || ($control->getCreatedAt() < $dateUpdate && $isEdited)) {
            /**************************** End of the temporary code *******************************************/

//        if ($this->logoInHeader == true) { /** Uncomment this code when you remove the previous code */
            $adherent = $control->getMinute()->getAdherent();

            /** Define Logo and set margin */
            $logo = $this->defineLogo($adherent);

            try {
                $header = $this->twig->render('@LuccaAdherent/Adherent/Printing/header-pdf.html.twig', array(
                    'adherent' => $adherent, 'officialLogo' => $logo
                ));
            } catch (Error $twig_Error) {
                echo 'Twig_Error has been thrown - Header Access Letter ' . $twig_Error->getMessage();
            }

            $this->options['header-html'] = $header;

            return $this->options;
        }

        return [];
    }

    /**
     * Define specific logo who was used
     * Increase Margin top if a logo is used
     */
    public function defineLogo(Adherent $adherent): null|string|Media
    {
        $logo = $this->logoFinder->findLogo($adherent);

        /** Increase margin-top */
        if ($logo) {
            $this->options['margin-top'] = 35;
        }

        return $logo;
    }
}
