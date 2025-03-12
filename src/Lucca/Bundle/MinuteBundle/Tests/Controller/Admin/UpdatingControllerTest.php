<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Updating;
use Symfony\Component\Routing\RouterInterface;

class UpdatingControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $updating = $em->getRepository(Updating::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_updating_new', [
                'minute_id' => $updating->getMinute()->getId(),
            ]), 302, 302),
            new UrlTest($router->generate('lucca_updating_step1', [
                'minute_id' => $updating->getMinute()->getId(), 'id' => $updating->getId(),
            ])),
        ];
    }
}
