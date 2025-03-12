<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Tests\Controller\Printing;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\FolderBundle\Entity\Courier;

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
        $courier = $em->getRepository(Courier::class)->findOneForTest();
        $minute = $courier->getFolder()->getMinute();

        /** Urls to test */
        return [
//            new UrlTest($router->generate('lucca_courier_offender_print', [
//                'minute_id' => $minute->getId(), 'id' => $courier->getId(),
//            ]), 302, 302),
//            new UrlTest($router->generate('lucca_courier_offender_preprint', [
//                'minute_id' => $minute->getId(), 'id' => $courier->getId(),
//            ]), 302, 302),
//            new UrlTest($router->generate('lucca_courier_judicial_print', [
//                'minute_id' => $minute->getId(), 'id' => $courier->getId(),
//            ]), 302, 302),
//            new UrlTest($router->generate('lucca_courier_judicial_preprint', [
//                'minute_id' => $minute->getId(), 'id' => $courier->getId(),
//            ]), 302, 302),
//            new UrlTest($router->generate('lucca_courier_ddtm_print', [
//                'minute_id' => $minute->getId(), 'id' => $courier->getId(),
//            ]), 302, 302),
//            new UrlTest($router->generate('lucca_courier_ddtm_preprint', [
//                'minute_id' => $minute->getId(), 'id' => $courier->getId(),
//            ]), 302, 302),
        ];
    }
}
