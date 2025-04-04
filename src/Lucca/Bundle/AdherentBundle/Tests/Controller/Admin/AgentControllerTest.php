<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\AdherentBundle\Entity\{Adherent, Agent};
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;

class AgentControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $adherent = $em->getRepository(Adherent::class)->findOneBy([
            'user' => $this->getUser(),
        ]);
        $agent = $em->getRepository(Agent::class)->findOneBy([
            'adherent' => $adherent
        ]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_agent_new', [
                'adh_id'=> $adherent->getId(),
            ])),
            new UrlTest($router->generate('lucca_agent_edit', [
                'id' => $agent->getId(), 'adh_id'=> $adherent->getId()
            ])),
            new UrlTest($router->generate('lucca_agent_enable', [ // disable
                'id' => $agent->getId(), 'adh_id'=> $adherent->getId()
            ]), 302, 302),
            new UrlTest($router->generate('lucca_agent_enable', [
                'id' => $agent->getId(), 'adh_id'=> $adherent->getId()
            ]), 302, 302),
        ];
    }
}
