<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\MinuteBundle\Entity\Minute;

class MinuteControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $minute = $em->getRepository(Minute::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_minute_index')),
            new UrlTest($router->generate('lucca_minute_new')),
            new UrlTest($router->generate('lucca_minute_show', ['id' => $minute->getId()])),
            new UrlTest($router->generate('lucca_minute_edit', ['id' => $minute->getId()])),
        ];
    }
}
