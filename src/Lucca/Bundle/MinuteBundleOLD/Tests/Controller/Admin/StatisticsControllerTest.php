<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\MinuteBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class StatisticsControllerTest
 * Test Lucca\MinuteBundle\Controller\Admin\StatisticsController
 *
 * @package Lucca\MinuteBundle\Tests\Controller\Admin
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class StatisticsControllerTest extends WebTestCase
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

        /**
         * Urls who was analyzed
         */
        $basicUrl = 'lucca_statistics_minutes_';
        $this->urls = array(
            $this->getUrl($basicUrl . 'overall', array()),
            $this->getUrl($basicUrl . 'table', array()),
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