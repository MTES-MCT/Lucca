<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\ContentBundle\Entity\Page;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;

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
        $page = $em->getRepository(Page::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_page_index')),
            new UrlTest($router->generate('lucca_page_new')),
            new UrlTest($router->generate('lucca_page_show', ['id' => $page->getId()])),
            new UrlTest($router->generate('lucca_page_edit', ['id' => $page->getId()])),
            new UrlTest($router->generate('lucca_page_enable', ['id' => $page->getId()]), 302, 302), // disable
            new UrlTest($router->generate('lucca_page_enable', ['id' => $page->getId()]), 302, 302),
        ];
    }
}
