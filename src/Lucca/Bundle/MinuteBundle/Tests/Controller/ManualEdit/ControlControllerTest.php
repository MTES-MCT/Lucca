<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Tests\Controller\ManualEdit;

use Lucca\Bundle\MinuteBundle\Entity\Control;
use Doctrine\ORM\EntityManager;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;

/**
 * Class ControlControllerTest
 * Test Lucca\Bundle\MinuteBundle\Controller\ManualEdit\ControlController
 *
 * @package Lucca\Bundle\MinuteBundle\Tests\Controller\ManualEdit
 * @author Terence <terence@numeric-wave.tech>
 */
class ControlControllerTest extends BasicLuccaTestCase
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
        $this->clientAuthenticated = $this->em->getRepository(User::Class)->findOneBy(array(
            'username' => 'lucca-nw-01'
        ));

        /**
         * Entity who was analysed
         */
        $this->entity = $this->em->getRepository(Control::class)->findOneBy(array(
            'type' => Control::TYPE_FOLDER
        ));

        $minute = $this->em->getRepository(Minute::class)->findMinuteByControl($this->entity);

        /**
         * Urls who was analyzed
         */
        $this->urls = array(
            $this->getUrl('lucca_control_access_manual', array('minute_id' => $minute->getId() , 'id' => $this->entity->getId())),
            $this->getUrl('lucca_control_convocation_manual', array('minute_id' => $minute->getId() , 'id' => $this->entity->getId())),
        );
    }

    /************************************ Test - routes reachable ************************************/

    /**
     * Test n°1 - No user authenticated
     * TODO bug if edition has a date edited
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
     * TODO bug if edition has a date edited
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