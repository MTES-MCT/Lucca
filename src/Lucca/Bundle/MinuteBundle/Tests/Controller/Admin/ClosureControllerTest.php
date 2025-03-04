<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\MinuteBundle\Entity\Closure;
use Lucca\Bundle\UserBundle\Entity\User;

/**
 * Class ClosureControllerTest
 * Test Lucca\Bundle\MinuteBundle\Controller\Admin\MinuteController
 *
 * @package Lucca\Bundle\MinuteBundle\Tests\Controller\Admin
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class ClosureControllerTest extends BasicLuccaTestCase
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
     * @var $entity
     * Entity to test
     */
    private $entity;

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
        $this->clientAuthenticated = $this->em->getRepository(User::class)->findOneBy(array(
            'username' => 'lucca-nw-01'
        ));

        /**
         * Entity who was analysed
         */
        $this->entity = $this->em->getRepository(Closure::class)->findOneBy(array());

        /**
         * Urls who was analyzed
         */
        $basicUrl = "lucca_minute_";
        $this->urls = array(
//            array('expectedCode' => 302, 'route' => $this->getUrl($basicUrl . 'open', array('id' => $this->entity->getMinute()->getId()))),
            array('expectedCode' => 200, 'route' => $this->getUrl($basicUrl . 'close', array('id' => $this->entity->getMinute()->getId()))),
        );
    }

    /************************************ Test - routes reachable ************************************/
    /**
     * Test n°1 - No User authenticated
     * If basic urls are blocked
     */
    public function testBasicUrlsAnonymous()
    {
        $this->defineBasicParams();

        /** Create client which test this action */
        $client = self::createClient();

        foreach ($this->urls as $url) {
            $client->request('GET', $url['route']);
            /** HTTP code attempted */
            self::assertStatusCode(302, $client);
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

        /** Log Log object + Create client which test this action */
        $this->loginAs($this->clientAuthenticated, 'main');
        $client = $this->makeClient();

        foreach ($this->urls as $url) {
            $client->request('GET', $url['route']);
            /** HTTP code attempted */
            self::assertStatusCode($url['expectedCode'], $client);
        }

        /** Close database connection */
        $this->em->getConnection()->close();
    }
}