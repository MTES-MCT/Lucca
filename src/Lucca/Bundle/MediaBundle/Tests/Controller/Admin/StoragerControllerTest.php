<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\MediaBundle\Entity\Storager;

class StoragerControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $storager = $em->getRepository(Storager::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_media_storager_index')),
            new UrlTest($router->generate('lucca_media_storager_new'), ['id' => $storager->getId()]),
            new UrlTest($router->generate('lucca_media_storager_show'), ['id' => $storager->getId()]),
            new UrlTest($router->generate('lucca_media_storager_edit'), ['id' => $storager->getId()]),
            new UrlTest($router->generate('lucca_media_storager_disable'), ['id' => $storager->getId()], 302, 302),
            new UrlTest($router->generate('lucca_media_storager_enable'), ['id' => $storager->getId()], 302, 302),
        ];
    }
}
