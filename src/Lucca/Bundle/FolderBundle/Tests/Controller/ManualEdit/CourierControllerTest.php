<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Tests\Controller\ManualEdit;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\FolderBundle\Entity\Courier;
use Lucca\Bundle\FolderBundle\Entity\CourierHumanEdition;
use Lucca\Bundle\MinuteBundle\Entity\Minute;

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
        $courierJudicial = $em->getRepository(Courier::class)->findOneForJudicialTest();
        $minuteCourierJudicial = $courierJudicial->getFolder()->getMinute();

        $courierDdtm = $em->getRepository(Courier::class)->findOneForDdtmTest();
        $minuteCourierDdtm = $courierDdtm->getFolder()->getMinute();

        $courierHumanEdition = $em->getRepository(CourierHumanEdition::class)->findOneEditionForTest();
        $minuteCourierHumanEdition = $courierHumanEdition->getCourier()->getFolder()->getMinute();

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_courier_manual_judicial', [
                'minute_id' => $minuteCourierJudicial->getId(), 'id' => $courierJudicial->getId(),
            ])),
            new UrlTest($router->generate('lucca_courier_manual_ddtm', [
                'minute_id' => $minuteCourierDdtm->getId(), 'id' => $courierDdtm->getId(),
            ])),
            new UrlTest($router->generate('lucca_courier_manual_offender', [
                'minute_id' => $minuteCourierHumanEdition->getId(), 'id' => $courierHumanEdition->getCourier()->getId()
            ])),
        ];
    }
}
