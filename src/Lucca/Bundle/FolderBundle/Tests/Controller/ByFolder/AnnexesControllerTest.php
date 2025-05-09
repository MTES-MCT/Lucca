<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Tests\Controller\ByFolder;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\FolderBundle\Entity\Folder;

class AnnexesControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $folder = $em->getRepository(Folder::class)->findOneBy([
            'type' => Folder::TYPE_FOLDER
        ]);

        /** Urls to test */
        return [
            // see setting.module.annexes.name
            new UrlTest($router->generate('lucca_annexes_edit', [
                'minute_id' => $folder->getMinute()->getId(), 'folder_id' => $folder->getId(),
            ]), 302, 302),
        ];
    }
}
