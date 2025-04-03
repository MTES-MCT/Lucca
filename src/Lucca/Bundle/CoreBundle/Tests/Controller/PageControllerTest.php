<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\ContentBundle\Entity\Page;

class PageControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $page = $em->getRepository(Page::class)->findOneBy(array(
            'name' => 'Glossaire'
        ));

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_core_page_tools')),
            new UrlTest($router->generate('lucca_core_page_show', ['slug' => $page->getSlug()])),
            new UrlTest($router->generate('lucca_core_page_show_print', ['slug' => $page->getSlug()])),
        ];
    }
}
