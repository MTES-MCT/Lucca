<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Tests\Abstract;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTestInterface;

abstract class BasicLuccaTestCase extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private RouterInterface $router;
    private UserInterface $clientAuthenticated;
    private Crawler $crawler;

    /**
     * Default username used to find the User entity
     */
    static string $usernameForTest = 'lucca-nw-01';

    /**
     * Provides URLs to test.
     * Force to implement this function by the real Web Test Case
     *
     * @return string[]
     */
    abstract protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array;

    public function getUser(): UserInterface
    {
        return $this->clientAuthenticated;
    }

    /************************************ Init Test functions ************************************/

    /**
     * This method is called before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        if ($_ENV['TEST_USERNAME']) {
            self::$usernameForTest = $_ENV['TEST_USERNAME'];
        }

        $this->client = static::createClient();

        $this->em = static::getContainer()->get('doctrine')->getManager();

        /** Get the router service to generate some url */
        $this->router = static::getContainer()->get('router');

        /** Get user to make test with an authenticated user */
        $this->clientAuthenticated = $this->em->getRepository(User::class)->loadUserByIdentifier(self::$usernameForTest);
    }

    /************************************ Test - routes reachable ************************************/

    /**
     * Test n°1 - No User has authenticated
     * Each url has been tested and check anonymous status
     */
    public function testUrlsAsAnonymous(): void
    {
        $urlsToBeTest = $this->getUrls($this->em, $this->router);

        foreach ($urlsToBeTest as $url) {
            if ($url instanceof UrlTestInterface) {
                $this->client->request($url->getMethod(), $url->getRoute(), $url->getFormData());
                /** HTTP code attempted */
                $this->assertResponseStatusCodeSame($url->getStatusAnonymous(), $this->client->getResponse()->getStatusCode());
            }
        }
    }

    /**
     * Test n°2 - User is authenticated
     * Each url has been tested and check anonymous status
     */
    public function testUrlsAsAuthenticatedUser(): void
    {
        /** Simulate clientAuthenticated being logged in */
        $this->client->loginUser($this->clientAuthenticated);

        $urlsToBeTest = $this->getUrls($this->em, $this->router);

        foreach ($urlsToBeTest as $url) {
            if ($url instanceof UrlTestInterface) {
                /**
                 * If the url is an edit form, we need to access its data a first time with a GET,
                 * then submit the values with the submit action
                 */
                $crawler = $this->client->request($url->getMethod(), $url->getRoute(), $url->getFormData());
                if ($url->getNeedSubmitForm()) {
                    $form = $crawler->filter('form')->first()->form();
                    $this->client->submit($form);
                }
                $this->assertResponseStatusCodeSame($url->getStatusAuth(), $this->client->getResponse()->getStatusCode());
            }
        }
    }
}
