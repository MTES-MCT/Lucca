<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\UserBundle\Entity\User;

class UserControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $user = $em->getRepository(User::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_user_index')),
            new UrlTest($router->generate('lucca_user_new')),
            new UrlTest($router->generate('lucca_user_show'), ['id' => $user->getId()]),
            new UrlTest($router->generate('lucca_user_edit'), ['id' => $user->getId()]),
            new UrlTest($router->generate('lucca_user_enable'), ['id' => $user->getId()], 302, 302), // disable
            new UrlTest($router->generate('lucca_user_enable'), ['id' => $user->getId()], 302, 302),
        ];
    }
}
