<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\FolderBundle\Entity\Folder;

class CourierControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $folder = $em->getRepository(Folder::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_courier_judicial_date', [
                'minute_id' => $folder->getMinute()->getId(), 'id' => $folder->getId(),
            ])),
        ];
    }
}
