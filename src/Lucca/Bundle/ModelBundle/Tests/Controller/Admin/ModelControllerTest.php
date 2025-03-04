<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\ModelBundle\Entity\Model;

class ModelControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $model = $em->getRepository(Model::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_model_index')),
            new UrlTest($router->generate('lucca_model_new')),
            new UrlTest($router->generate('lucca_model_show', ['id' => $model->getId()])),
            new UrlTest($router->generate('lucca_model_edit', ['id' => $model->getId()])),
            new UrlTest($router->generate('lucca_model_enable', ['id' => $model->getId()])), // disable
            new UrlTest($router->generate('lucca_model_enable', ['id' => $model->getId()])),
        ];
    }
}
