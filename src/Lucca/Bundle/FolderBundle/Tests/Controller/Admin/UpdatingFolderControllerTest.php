<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Updating;

class UpdatingFolderControllerTest extends BasicLuccaTestCase
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
            'type' => Folder::TYPE_REFRESH
        ]);
        $minute = $folder->getMinute();

        $updating = $em->getRepository(Updating::class)->findUpdatingByControl($folder->getControl()) ;

        /** Urls to test */
        return [
            new UrlTest($router->generate('lucca_updating_folder_new', [
                'minute_id' => $minute->getId(), 'updating_id' => $updating->getId()
            ])),
            new UrlTest($router->generate('lucca_updating_folder_edit', [
                'minute_id' => $minute->getId(), 'updating_id' => $updating->getId(), 'id' => $folder->getId()
            ])),
            new UrlTest($router->generate('lucca_updating_folder_fence', [
                'minute_id' => $minute->getId(), 'updating_id' => $updating->getId(), 'id' => $folder->getId()
            ]), 302, 302),
        ];
    }
}
