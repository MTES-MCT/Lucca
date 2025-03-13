<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;

class AdherentControllerTest extends BasicLuccaTestCase
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

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_adherent_index')),
            new UrlTest($router->generate('lucca_adherent_new')),
            new UrlTest($router->generate('lucca_adherent_show', ['id' => $adherent->getId()])),
            new UrlTest($router->generate('lucca_adherent_edit', ['id' => $adherent->getId()])),
            new UrlTest($router->generate('lucca_adherent_disable', ['id' => $adherent->getId()]), 302, 302),
            new UrlTest($router->generate('lucca_adherent_enable', ['id' => $adherent->getId()]), 302, 302),
        ];
    }
}
