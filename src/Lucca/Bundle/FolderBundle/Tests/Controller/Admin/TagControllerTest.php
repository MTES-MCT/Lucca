<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\FolderBundle\Entity\Tag;

class TagControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $folder = $em->getRepository(Tag::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_tag_index')),
            new UrlTest($router->generate('lucca_tag_new')),
            new UrlTest($router->generate('lucca_tag_show', ['id' => $folder->getId()])),
            new UrlTest($router->generate('lucca_tag_edit', ['id' => $folder->getId()])),
            new UrlTest($router->generate('lucca_tag_enable', ['id' => $folder->getId()]), 302, 302), // disable
            new UrlTest($router->generate('lucca_tag_enable', ['id' => $folder->getId()]), 302, 302),
        ];
    }
}
