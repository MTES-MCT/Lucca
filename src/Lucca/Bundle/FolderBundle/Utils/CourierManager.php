<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Lucca\Bundle\FolderBundle\Entity\{Courier, Folder};

readonly class CourierManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack           $requestStack,
        private CourierEditionManager  $courierEditionManager,
    )
    {
    }

    /**
     * Create Courier after Folder fence
     */
    public function createCourierToFolder(Folder $folder): Courier
    {
        $courier = $this->em->getRepository(Courier::class)->findOneBy([
            'folder' => $folder
        ]);

        if (!$courier) {
            $courier = new Courier();
            $courier->setFolder($folder);
        }

        /** Create / update / delete editions if needed */
        $this->courierEditionManager->manageEditionsOnFormSubmission($courier);
        $this->requestStack->getSession()->getFlashBag()->add('info', 'flash.courier.addSuccessfully');

        $this->em->persist($courier);

        return $courier;
    }
}
