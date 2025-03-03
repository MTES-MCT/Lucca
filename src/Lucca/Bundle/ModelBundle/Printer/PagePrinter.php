<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Printer;

use Twig\Environment;
use Twig\Error\Error;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\ModelBundle\Entity\Model;

class PagePrinter
{
    public function __construct(
        private readonly Environment $twig,
        private readonly TranslatorInterface $translator,
        private array $options,
    )
    {
        $this->options = [
            'debug-javascript' => true,
            'enable-javascript' => true,
//            'javascript-delay' => 1500,
            'margin-top' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
            'margin-right' => 0,
            'dpi' => 100,
        ];
    }

    /**
     * Create option for model
     *
     * @param Model $model model for which we want to create the options
     */
    public function createModelOption(Model $model, array $var = [], $adherent = null): array
    {
        $var = $this->varIndexer($var);

        $page = $model->getRecto();
        if ($model->getLayout() === Model::LAYOUT_SIMPLE) {
            $renderParameters = array('page' => $page, 'var' => $var, 'adherent' => $adherent);
        } else {
            $renderParameters = array('page' => $page, 'var' => $var, 'coverPage' => $model->getVerso(), 'cover' => true, 'adherent' => $adherent);
        }

        $this->options['page-size'] = $this->translator->trans($model->getSize(), array(), 'LuccaModelBundle');
        $this->options['orientation'] = $this->translator->trans('pdf.' . $model->getOrientation(), array(), 'LuccaModelBundle');

        /* Margins */
        if ($page->getMarginTop() === null) {
            $this->options['margin-top'] = $this->pixelTomm($page->getHeaderSize());
        } else {
            /** 0.19 resulting of test that able the header to stick to the top of the page in every sizes */
            $this->options['margin-top'] = $this->pixelTomm($page->getMarginTop()->getHeight());
//            $this->options['margin-top'] = $page->getMarginTop()->getHeight() * 0.19;
        }
        if ( $page->getMarginBottom() === null) {
            $this->options['margin-bottom'] = $this->pixelTomm($page->getFooterSize());
        } else {
            /** 0.21 resulting of test that able the footer to stick to the bottom of the page in every sizes */
            $this->options['margin-bottom'] = $this->pixelTomm($page->getMarginBottom()->getHeight());
//            $this->options['margin-bottom'] = $page->getMarginBottom()->getHeight() * 0.21;
        }

        /** If there is custom margin we need to set left and right margin to 0 */
        $this->options['margin-right'] = $page->getMarginRight() === null ? $this->pixelTomm($page->getRightSize()) : 0;
        $this->options['margin-left'] = $page->getMarginLeft() === null ? $this->pixelTomm($page->getLeftSize()) : 0;

        $header = "";
        try {
            $header = $this->twig->render('@LuccaModel/Printing/header.html.twig', $renderParameters);
        } catch (Error $twig_Error) {

            // todo use better fix when the $renderParameters have bad parameters (is not due to this new function)
            echo 'Twig_Error has been thrown - Header model ' . $twig_Error->getMessage();
        }
        $this->options['header-html'] = $header;

        $footer = "";
        try {
            $footer = $this->twig->render('@LuccaModel/Printing/footer.html.twig', $renderParameters);
        } catch (Error $twig_Error) {
            // todo use better fix when the $renderParameters have bad parameters (is not due to this new function)
            echo 'Twig_Error has been thrown - Footer Model ' . $twig_Error->getMessage();
        }

        $this->options['footer-html'] = $footer;

        return $this->options;
    }

    /**
     /* DPI * Pixel / inch (into mm)
     */
    public function mmToPixel(int $mm): float
    {
        return $this->options["dpi"] * $mm / 25.4;
    }

    /**
     * mm = Pixel * inch(into mm) / DPI
     */
    public function pixelTomm(int $pixel): float
    {
        /* Pixel * inch (into mm) / DPI */
        return $pixel * 25.4 / $this->options["dpi"];
    }

    /**
     * function change key into ${key}
     */
    public function varIndexer(array $var): array
    {
        $index = [];

        foreach ($var as $key => $value) {
            $index["\${{$key}}"] = $value;
        }

        return $index;
    }

    public function getName(): string
    {
        return 'lucca.utils.printer.model.page';
    }
}
