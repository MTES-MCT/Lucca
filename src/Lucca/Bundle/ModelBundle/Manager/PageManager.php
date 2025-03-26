<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Manager;

use Symfony\Component\HttpFoundation\RequestStack;

use Lucca\Bundle\ModelBundle\Entity\{Margin, Model, Page};

readonly class PageManager
{
    public function __construct(
        private RequestStack           $requestStack,
    )
    {
    }

    /**
     * Page manager during edition
     */
    public function manageMarginsOnPage(Model $model, Page $page, Page $previousPage): ?Page
    {
        /* remove Margin if there have size */
        /* setDefault HeaderSize */
        if ($page->getHeaderSize() === null && $page->getMarginTop() === null) {
            $page->setHeaderSize(0);
        }

        /* delete MarginObject in page */
        if ($previousPage->getHeaderSize() === null && $page->getHeaderSize() !== null) {
            $page->setMarginTop(null);
        }

        /* Footer Margin */
        /* setDefault HeaderSize */
        if ($page->getFooterSize() === null && $page->getMarginBottom() === null) {
            $page->setFooterSize(0);
        }

        /* delete MarginObject in page */
        if ($previousPage->getFooterSize() === null && $page->getFooterSize() !== null) {
            $page->setMarginBottom(null);
        }

        /* Left Margin */
        /* setDefault HeaderSize */
        if ($page->getLeftSize() === null && $page->getMarginLeft() === null) {
            $page->setLeftSize(0);
        }

        /* delete MarginObject in page */
        if ($previousPage->getLeftSize() === null && $page->getLeftSize() !== null) {
            $page->setMarginLeft(null);
        }

        /* Right Margin */
        /* setDefault HeaderSize */
        if ($page->getRightSize() === null && $page->getMarginRight() == null) {
            $page->setRightSize(0);
        }

        /* delete MarginObject in page */
        if ($previousPage->getRightSize() === null && $page->getRightSize() !== null) {
            $page->setMarginRight(null);
        }

        /** Multiplier used to set default size of margin */
        $multiplier = 1.23;

        /* check valid Margin */
        if (($margin = $page->getMarginTop()) != null) {

            if (!$this->isValidHorizontalMargin($margin)) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', 'flash.margin.heightMustBeDefined');

                return null;
            }

            $margin->setPosition(Margin::POSITION_TOP);
            $margin->setWidth($model->getMaxWidth() * $multiplier);
        }

        if (($margin = $page->getMarginBottom()) != null) {

            if (!$this->isValidHorizontalMargin($margin)) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', 'flash.margin.heightMustBeDefined');

                return null;
            }

            $margin->setPosition(Margin::POSITION_BOTTOM);
            $margin->setWidth($model->getMaxWidth() * $multiplier);
        }

        if (($margin = $page->getMarginLeft()) != null) {

            if (!$this->isValidVerticalMargin($margin)) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', 'flash.margin.widthMustBeDefined');

                return null;
            }

            $margin->setPosition(Margin::POSITION_LEFT);
            $margin->setHeight(($model->getMaxHeight() * $multiplier) - ($page->getHeightBottom() + $page->getHeightTop()));
        }

        if (($margin = $page->getMarginRight()) != null) {

            if (!$this->isValidVerticalMargin($margin)) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', 'flash.margin.widthMustBeDefined');

                return null;
            }

            $margin->setPosition(Margin::POSITION_RIGHT);
            $margin->setHeight(($model->getMaxHeight() * $multiplier) - ($page->getHeightBottom() + $page->getHeightTop()));
        }

        return $page;
    }

    /**
     * Check if Margin is valid
     */
    public function isValidHorizontalMargin(?Margin $p_margin = null): bool
    {
        return $p_margin->getHeight() !== null;
    }

    /**
     * Check if Margin is valid
     */
    public function isValidVerticalMargin(Margin $p_margin = null): bool
    {
        return $p_margin->getWidth() !== null;
    }
}
