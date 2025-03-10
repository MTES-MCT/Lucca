<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\ParameterBundle\Entity\Town;

class TownControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $town = $em->getRepository(Town::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_town_index')),
            new UrlTest($router->generate('lucca_town_new')),
            new UrlTest($router->generate('lucca_town_show', ['id' => $town->getId()])),
            new UrlTest($router->generate('lucca_town_edit', ['id' => $town->getId()])),
            new UrlTest($router->generate('lucca_town_enable', ['id' => $town->getId()]), 302, 302), // disable
            new UrlTest($router->generate('lucca_town_enable', ['id' => $town->getId()]), 302, 302),
        ];
    }
}
