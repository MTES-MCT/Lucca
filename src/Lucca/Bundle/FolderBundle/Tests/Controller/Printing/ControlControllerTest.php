<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Tests\Controller\Printing;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

use Lucca\Bundle\CoreBundle\Tests\Abstract\BasicLuccaTestCase;
use Lucca\Bundle\CoreBundle\Tests\Model\UrlTest;
use Lucca\Bundle\MinuteBundle\Entity\Control;

class ControlControllerTest extends BasicLuccaTestCase
{
    /**
     * Create all urls which been tests
     */
    protected function getUrls(EntityManagerInterface $em, RouterInterface $router): array
    {
        /**
         * Entity to use for the tests
         */
        $folder = $em->getRepository(Control::class)->findOneForTest(Control::TYPE_FOLDER);

        $attemptedCode = 200;
        if ($folder->getHumansByMinute()->isEmpty() && $folder->getHumansByControl()->isEmpty() && $folder->getEditions()->isEmpty()) {
            $attemptedCode = 302;
        }

        /** Urls to test */
        return [
//            new UrlTest($router->generate('lucca_control_access_print', [
//                'minute_id' => $folder->getMinute()->getId(), 'id' => $folder->getId(),
//            ]), 302, $attemptedCode),
//            new UrlTest($router->generate('lucca_control_letter_print', [
//                'minute_id' => $folder->getMinute()->getId(), 'id' => $folder->getId(),
//            ]), 302, $attemptedCode),
        ];
    }
}
