<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\MinuteBundle\Tests\Controller\Admin;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class FolderControllerTest
 * Test Lucca\MinuteBundle\Controller\Admin\MinuteController
 *
 * @package Lucca\MinuteBundle\Tests\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class FolderControllerTest extends WebTestCase
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
        $this->clientAuthenticated = $this->em->getRepository('LuccaUserBundle:User')->findOneBy(array(
            'username' => 'lucca-nw-01'
        ));

        /**
         * Entity who was analysed
         */
        $this->entity = $this->em->getRepository('LuccaMinuteBundle:Folder')->findOneBy(array(
            'type' => Folder::TYPE_FOLDER
        ));

        $minute = $this->entity->getMinute();

        /**
         * Urls who was analyzed
         */
        $basicUrl = 'lucca_folder_';
        $this->urls = array(
            array('expectedCode' => 200, 'route' => $this->getUrl($basicUrl . 'new', array('minute_id' => $minute->getId()))),
            array('expectedCode' => 200, 'route' => $this->getUrl($basicUrl . 'step1', array('minute_id' => $minute->getId(), 'id' => $this->entity->getId()))),
            array('expectedCode' => 200, 'route' => $this->getUrl($basicUrl . 'step2', array('minute_id' => $minute->getId(), 'id' => $this->entity->getId()))),
            array('expectedCode' => 200, 'route' => $this->getUrl($basicUrl . 'step3', array('minute_id' => $minute->getId(), 'id' => $this->entity->getId()))),
            array('expectedCode' => 200, 'route' => $this->getUrl($basicUrl . 'edit', array('minute_id' => $minute->getId(), 'id' => $this->entity->getId()))),
            array('expectedCode' => 302, 'route' => $this->getUrl($basicUrl . 'fence', array('minute_id' => $minute->getId(), 'id' => $this->entity->getId()))),
            array('expectedCode' => 302, 'route' => $this->getUrl($basicUrl . 'open', array('minute_id' => $minute->getId(), 'id' => $this->entity->getId()))),
            array('expectedCode' => 302, 'route' => $this->getUrl($basicUrl . 'reread', array('minute_id' => $minute->getId(), 'id' => $this->entity->getId()))),
            array('expectedCode' => 302, 'route' => $this->getUrl($basicUrl . 'unreread', array('minute_id' => $minute->getId(), 'id' => $this->entity->getId()))),
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