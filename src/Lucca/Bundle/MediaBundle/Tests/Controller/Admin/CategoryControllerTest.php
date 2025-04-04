<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\MediaBundle\Entity\Category;

class CategoryControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $category = $em->getRepository(Category::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_media_category_index')),
            new UrlTest($router->generate('lucca_media_category_new', ['id' => $category->getId()])),
            new UrlTest($router->generate('lucca_media_category_show', ['id' => $category->getId()])),
            new UrlTest($router->generate('lucca_media_category_edit', ['id' => $category->getId()])),
            new UrlTest($router->generate('lucca_media_category_disable', ['id' => $category->getId()]), 302, 302),
            new UrlTest($router->generate('lucca_media_category_enable', ['id' => $category->getId()]), 302, 302),
        ];
    }
}
