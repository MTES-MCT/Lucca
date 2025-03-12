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

use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\MinuteBundle\Entity\{Control, Updating};

class UpdatingControlControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $control = $em->getRepository(Control::class)->findOneBy([
            'type' => Control::TYPE_REFRESH,
        ]);

        $updating = $em->getRepository(Updating::class)->findUpdatingByControl($control);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_updating_control_new', ['updating_id' => $updating->getId()])),
            new UrlTest($router->generate('lucca_updating_control_edit', [
                'updating_id' => $updating->getId() , 'id' => $control->getId(),
            ])),
        ];
    }
}
