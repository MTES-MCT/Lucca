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
use Lucca\Bundle\MediaBundle\Entity\Media;

class MediaControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $media = $em->getRepository(Media::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_media_index')),
            new UrlTest($router->generate('lucca_media_disable', ['id' => $media->getId()]), 302, 302),
            new UrlTest($router->generate('lucca_media_enable', ['id' => $media->getId()]), 302, 302),
        ];
    }
}
