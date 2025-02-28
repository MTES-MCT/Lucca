<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Tests\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\MediaBundle\Entity\{Category, Extension, Media};

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
        $category = $em->getRepository(Category::class)->findOneBy([]);
        $extension = $em->getRepository(Extension::class)->findOneBy([]);
        $media = $em->getRepository(Media::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_media_api_meta_datas'), ['categoryId' => $category->getId(),]),
            new UrlTest($router->generate('lucca_media_api_meta_datas_by_extension'), ['extension' => $extension->getId()]),
            new UrlTest($router->generate('lucca_media_api_get_delete_modal'), ['id' => $media->getId()]),
        ];
    }
}
