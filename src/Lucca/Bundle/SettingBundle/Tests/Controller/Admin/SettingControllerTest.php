<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\SettingBundle\Entity\Setting;

class SettingControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $setting = $em->getRepository(Setting::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_setting_index')),
            new UrlTest($router->generate('lucca_setting_edit', ['id' => $setting->getId()])),
            new UrlTest($router->generate('lucca_setting_show', ['id' => $setting->getId()])),
        ];
    }
}
