<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ChecklistBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\ChecklistBundle\Entity\Checklist;
use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;

class ChecklistControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $checklist = $em->getRepository(Checklist::class)->findOneBy([]);

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_checklist_index')),
            new UrlTest($router->generate('lucca_checklist_new')),
            new UrlTest($router->generate('lucca_checklist_show', ['id' => $checklist->getId()])),
            new UrlTest($router->generate('lucca_checklist_edit', ['id' => $checklist->getId()])),
            new UrlTest($router->generate('lucca_checklist_enable', ['id' => $checklist->getId()])), // disable
            new UrlTest($router->generate('lucca_checklist_enable', ['id' => $checklist->getId()])),
        ];
    }
}
