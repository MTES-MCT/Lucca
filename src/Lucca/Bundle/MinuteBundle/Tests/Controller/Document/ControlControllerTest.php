<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Tests\Controller\Document;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\MinuteBundle\Entity\{Control, Minute};

class ControlControllerTest extends BasicLuccaTestCase
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
            'type' => Control::TYPE_FOLDER,
        ]);

        $minute = $em->getRepository(Minute::class)->findMinuteByControl($control);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_control_access', [
                'minute_id' => $minute->getId(), 'id' => $control->getId(),
            ])),
            new UrlTest($router->generate('lucca_control_letter', [
                'minute_id' => $minute->getId(), 'id' => $control->getId(),
            ])),
        ];
    }
}
