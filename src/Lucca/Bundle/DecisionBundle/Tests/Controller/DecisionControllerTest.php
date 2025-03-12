<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\DecisionBundle\Entity\Decision;

class DecisionControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $decision = $em->getRepository(Decision::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_decision_new', [
                'minute_id' => $decision->getMinute()->getId(), 'id' => $decision->getId(),
            ])),
            new UrlTest($router->generate('lucca_decision_edit', [
                'minute_id' => $decision->getMinute()->getId(), 'id' => $decision->getId(),
            ])),
        ];
    }
}
