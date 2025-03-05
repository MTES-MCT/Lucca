<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;

use Lucca\Bundle\FolderBundle\Entity\{Courier, Folder};
use Lucca\Bundle\MinuteBundle\Entity\{Control, Minute, Updating};
use Lucca\Bundle\FolderBundle\Utils\{CourierEditionManager, FolderEditionManager, FolderManager};

readonly class ClosureManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ControlEditionManager  $controlEditionManager,
        private FolderManager          $folderManager,
        private FolderEditionManager   $folderEditionManager,
        private CourierEditionManager  $courierEditionManager
    )
    {
    }

    public function closeMinute(Minute $minute): Minute
    {
        /** Get minute values to Frame 2/ 3 / 4 */
        $controls = [];
        $folders = [];
        $couriers = [];

        foreach ($minute->getControls() as $control) {
            $loopControls = $this->em->getRepository(Control::class)->find($control->getId());

            foreach ($loopControls as $loopControl) {
                $controls[] = $loopControl;
            }

            $loopFolders = $this->em->getRepository(Folder::class)->findFolderByControl($control);

            foreach ($loopFolders as $loopFolder) {
                $folders[] = $loopFolder;
            }
        }

        foreach ($folders as $loopFolder) {
            $loopCouriers = $this->em->getRepository(Courier::class)->findCouriersByFolder($loopFolder);

            foreach ($loopCouriers as $loopCourier) {
                $couriers[] = $loopCourier;
            }
        }

        /** Purge Frame 2 */
        foreach ($controls as $control) {
            $this->controlEditionManager->purgeEditions($control);
        }

        /** Purge Frame 3 */
        foreach ($folders as $folder) {
            $this->folderManager->closeFolder($folder);
            $this->folderManager->purgeChecklist($folder);
            $this->folderEditionManager->purgeEdition($folder);
        }

        /** Purge Frame 4 */
        foreach ($couriers as $courier) {
            $this->courierEditionManager->purgeEditions($courier);
        }

        /** Get updating values to Frame 6 */
        $updates = $this->em->getRepository(Updating::class)->findByMinute($minute);

        $up_controls = [];
        $up_folders = [];
        $up_couriers = [];

        foreach ($updates as $update) {

            foreach ($update->getControls() as $control) {

                $loopControls = $this->em->getRepository(Control::class)->find($control->getId());

                foreach ($loopControls as $loopControl) {
                    $up_controls[] = $loopControl;
                }

                $loopFolders = $this->em->getRepository(Folder::class)->findFolderByControl($control);

                foreach ($loopFolders as $loopFolder) {
                    $up_folders[] = $loopFolder;
                }
            }

            foreach ($up_folders as $loopFolder) {
                $loopCouriers = $this->em->getRepository(Courier::class)->findCouriersByFolder($loopFolder);

                foreach ($loopCouriers as $loopCourier) {
                    $up_couriers[] = $loopCourier;
                }
            }
        }

        /** Purge datas */
        foreach ($up_controls as $control) {
            $this->controlEditionManager->purgeEditions($control);
        }

        foreach ($up_folders as $folder) {
            $this->folderManager->closeFolder($folder);
            $this->folderManager->purgeChecklist($folder);
            $this->folderEditionManager->purgeEdition($folder);
        }

        foreach ($up_couriers as $courier) {
            $this->courierEditionManager->purgeEditions($courier);
        }

        return $minute;
    }

    public function getName(): string
    {
        return 'lucca.manager.closure';
    }
}
