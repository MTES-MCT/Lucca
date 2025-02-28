<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\MinuteBundle\Tests\Controller\ManualEdit;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier;
use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierHumanEdition;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CourierControllerTest
 * Test Lucca\MinuteBundle\Controller\ManualEdit\CourierController
 *
 * @package Lucca\MinuteBundle\Tests\Controller\ManualEdit
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierControllerTest extends WebTestCase
{
    /**
     * @var $urls
     * All urls tested
     */
    private $urls;

    /**
     * @var $clientAuthenticated
     * Client which can authenticated
     */
    private $clientAuthenticated;

    /**
     * @var EntityManager
     */
    private $em;

    /************************************ Init Test functions ************************************/

    /**
     * Define basic urls to test
     */
    private function defineBasicParams()
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        /**
         * Client who can authenticated in firewall
         */
        $this->clientAuthenticated = $this->em->getRepository('LuccaUserBundle:User')->findOneBy(array(
            'username' => 'lucca-nw-01'
        ));

        /** @var Courier $courierJudicial */
        $courierJudicial = $this->em->getRepository('LuccaMinuteBundle:Courier')->findOneForJudicialTest();
        /** @var Minute $minuteCourierJudicial */
        $minuteCourierJudicial = $courierJudicial->getFolder()->getMinute();

        /** @var Courier $courierDdtm */
        $courierDdtm = $this->em->getRepository('LuccaMinuteBundle:Courier')->findOneForDdtmTest();
        /** @var Minute $minuteCourierDdtm */
        $minuteCourierDdtm = $courierDdtm->getFolder()->getMinute();

        /** @var CourierHumanEdition $courierHumanEdition */
        $courierHumanEdition = $this->em->getRepository('LuccaMinuteBundle:CourierHumanEdition')->findOneEditionForTest();
        /** @var Minute $minuteCourierHumanEdition */
        $minuteCourierHumanEdition = $courierHumanEdition->getCourier()->getFolder()->getMinute();

        /**
         * Urls who was analyzed
         */
        $this->urls = array(
            $this->getUrl('lucca_courier_manual_judicial', array('minute_id' => $minuteCourierJudicial->getId(), 'id' => $courierJudicial->getId())),
            $this->getUrl('lucca_courier_manual_ddtm', array('minute_id' => $minuteCourierDdtm->getId(), 'id' => $courierDdtm->getId())),

            $this->getUrl('lucca_courier_manual_offender', array('minute_id' => $minuteCourierHumanEdition->getId(), 'id' => $courierHumanEdition->getCourier()->getId())),
        );
    }

    /************************************ Test - routes reachable ************************************/

    /**
     * Test n°1 - No user authenticated
     * If basic urls are blocked
     */
    public function testBasicUrlsAnonymous()
    {
        $this->defineBasicParams();

        /** Create client which test this action */
        $client = $this->createClient();

        foreach ($this->urls as $url) {
            $client->request('GET', $url);

            /** HTTP code attempted */
            $this->assertStatusCode(302, $client);
        }

        /** Close database connection */
        $this->em->getConnection()->close();
    }

    /**
     * Test n°2 - User is authenticated
     * If basic urls are reachable
     */
    public function testBasicUrlsWithAuthUser()
    {
        $this->defineBasicParams();

        /** Log User object + Create client which test this action */
        $this->loginAs($this->clientAuthenticated, 'main');
        $client = $this->makeClient();

        foreach ($this->urls as $url) {
            $client->request('GET', $url);

            /** HTTP code attempted */
            $this->assertStatusCode(200, $client);
        }

        /** Close database connection */
        $this->em->getConnection()->close();
    }
}