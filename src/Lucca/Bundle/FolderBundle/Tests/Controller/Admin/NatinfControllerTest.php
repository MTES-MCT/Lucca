<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\FolderBundle\Entity\Natinf;

class NatinfControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $natinf = $em->getRepository(Natinf::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_natinf_index')),
            new UrlTest($router->generate('lucca_natinf_new')),
            new UrlTest($router->generate('lucca_natinf_show', ['id' => $natinf->getId()])),
            new UrlTest($router->generate('lucca_natinf_edit', ['id' => $natinf->getId()])),
            new UrlTest($router->generate('lucca_natinf_enable', ['id' => $natinf->getId()]), 302, 302), // disable
            new UrlTest($router->generate('lucca_natinf_enable', ['id' => $natinf->getId()]), 302, 302),
        ];
    }
}
