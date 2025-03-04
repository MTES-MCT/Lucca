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

use Lucca\Bundle\ContentBundle\Entity\Area;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;

class AreaControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $area = $em->getRepository(Area::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_area_index')),
            new UrlTest($router->generate('lucca_area_new')),
            new UrlTest($router->generate('lucca_area_show', ['id' => $area->getId()])),
            new UrlTest($router->generate('lucca_area_edit', ['id' => $area->getId()])),
            new UrlTest($router->generate('lucca_area_enable', ['id' => $area->getId()])), // disable
            new UrlTest($router->generate('lucca_area_enable', ['id' => $area->getId()])),
        ];
    }
}
