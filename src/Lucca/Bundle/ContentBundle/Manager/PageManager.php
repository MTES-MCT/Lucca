<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

use Lucca\Bundle\ContentBundle\Entity\{Area, Page};
use Lucca\Bundle\CoreBundle\Utils\Canonalizer;
use Lucca\Bundle\MediaBundle\Entity\Media;

readonly class PageManager
{
    public function __construct(
        private EntityManagerInterface  $em,
        private Canonalizer             $canonalizer,
    )
    {
    }

    /**
     * Manage medias linked to a Page entity
     *
     * @param Page $page
     * @return mixed
     */
    public function managePageAndMediasLinked(Page $page): Page
    {
        /** @var Media $media */
        foreach ($page->getMediasLinked() as $media) {

            if ($media && $media->getId() !== null && $media->getName() === null) {
                $page->removeMediasLinked($media);

                try {
                    $this->em->remove($media);
                } catch (ORMException $ORMException) {
                    echo 'ORMException has been thrown - Page media ' . $ORMException->getMessage();
                }
            }

            // Set the media public if the page is in the content area
            if ($media->getId() === null && $page->getSubarea()->getArea()->getPosition() === Area::POSI_CONTENT) {
                $media->setPublic(true);
            }
        }

        $page->setSlug($this->canonalizer->slugify($page->getName()));

        return $page;
    }
}
