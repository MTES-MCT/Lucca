<?php

namespace Lucca\Bundle\CoreBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;

class StatisticsControllerTest  extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_core_statistics')),
        ];
    }
}
