<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTestDefinition;
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
            new UrlTestDefinition($router->generate('lucca_model_index')),
            new UrlTestDefinition($router->generate('lucca_model_new')),
            new UrlTestDefinition($router->generate('lucca_model_show', ['id' => $model->getId()])),
            new UrlTestDefinition($router->generate('lucca_model_edit', ['id' => $model->getId()])),
            new UrlTestDefinition($router->generate('lucca_model_enable', ['id' => $model->getId()]), 302, 302), // disable
            new UrlTestDefinition($router->generate('lucca_model_enable', ['id' => $model->getId()]), 302, 302),
        ];
    }
}
