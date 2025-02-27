<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */


 

namespace Lucca\Bundle\DepartmentBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\DepartmentBundle\Entity\Department;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;

/**
 * Class DepartmentControllerTest
 *
 * @package Lucca\Bundle\DepartmentBundle\Tests\Controller\Admin
 */
class DepartmentControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity who was analysed
         *
         * /** @var Department $entity
         */
        $entity = $em->getRepository(Department::class)->findOneBy(array());

        /** Urls who was analyzed */
        return array(
            new UrlTest($router->generate('lucca_department_admin_index', array()),
            ),
            new UrlTest($router->generate('lucca_department_admin_new', array(),
                302, 200, 'GET', true),
            ),
            new UrlTest($router->generate('lucca_department_admin_show', array('id' => $entity->getId())),
            ),
            new UrlTest($router->generate('lucca_department_admin_edit', array('id' => $entity->getId())),
                302, 302, 'GET', true
            ),
        );
    }
}
