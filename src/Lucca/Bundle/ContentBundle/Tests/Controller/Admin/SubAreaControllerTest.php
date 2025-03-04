<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\ContentBundle\Entity\SubArea;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;

class SubAreaControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $subArea = $em->getRepository(SubArea::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_subarea_index')),
            new UrlTest($router->generate('lucca_subarea_new')),
            new UrlTest($router->generate('lucca_subarea_show', ['id' => $subArea->getId()])),
            new UrlTest($router->generate('lucca_subarea_edit', ['id' => $subArea->getId()])),
            new UrlTest($router->generate('lucca_subarea_enable', ['id' => $subArea->getId()])), // disable
            new UrlTest($router->generate('lucca_subarea_enable', ['id' => $subArea->getId()])),
        ];
    }
}
