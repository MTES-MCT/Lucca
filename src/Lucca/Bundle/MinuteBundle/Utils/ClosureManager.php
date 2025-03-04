<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Utils;

use Lucca\Bundle\FolderBundle\Entity\Courier;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Doctrine\ORM\EntityManager;

/**
 * Class ClosureManager
 *
 * @package Lucca\Bundle\MinuteBundle\Utils
 * @author Térence <terence@numeric-wave.tech>
 */
class ClosureManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ControlEditionManager
     */
    private $controlEditionManager;

    /**
     * @var FolderManager
     */
    private $folderManager;

    /**
     * @var FolderEditionManager
     */
    private $folderEditionManager;

    /**
     * @var CourierEditionManager
     */
    private $courierEditionManager;

    /**
     * ClosureManager constructor
     *
     * @param EntityManager $entityManager
     * @param ControlEditionManager $controlEditionManager
     * @param FolderManager $folderManager
     * @param FolderEditionManager $folderEditionManager
     * @param CourierEditionManager $courierEditionManager
     */
    public function __construct(EntityManager $entityManager, ControlEditionManager $controlEditionManager,
                                FolderManager $folderManager, FolderEditionManager $folderEditionManager,
                                CourierEditionManager $courierEditionManager)
    {
        $this->em = $entityManager;
        $this->controlEditionManager = $controlEditionManager;
        $this->folderManager = $folderManager;
        $this->folderEditionManager = $folderEditionManager;
        $this->courierEditionManager = $courierEditionManager;
    }


    public function closeMinute(Minute $minute)
    {
        /** Get minute values to Frame 2/ 3 / 4 */
        $controls = array();
        $folders = array();
        $couriers = array();

        foreach ($minute->getControls() as $control) {
            $loopControls = $this->em->getRepository(Control::class)->find($control->getId());

            foreach ($loopControls as $loopControl)
                $controls[] = $loopControl;

            $loopFolders = $this->em->getRepository(Folder::class)->findFolderByControl($control);

            foreach ($loopFolders as $loopFolder)
                $folders[] = $loopFolder;
        }

        foreach ($folders as $loopFolder) {
            $loopCouriers = $this->em->getRepository(Courier::class)->findCouriersByFolder($loopFolder);

            foreach ($loopCouriers as $loopCourier)
                $couriers[] = $loopCourier;
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

        $up_controls = array();
        $up_folders = array();
        $up_couriers = array();

        foreach ($updates as $update) {

            foreach ($update->getControls() as $control) {

                $loopControls = $this->em->getRepository(Control::class)->find($control->getId());

                foreach ($loopControls as $loopControl)
                    $up_controls[] = $loopControl;

                $loopFolders = $this->em->getRepository(Folder::class)->findFolderByControl($control);

                foreach ($loopFolders as $loopFolder)
                    $up_folders[] = $loopFolder;
            }

            foreach ($up_folders as $loopFolder) {
                $loopCouriers = $this->em->getRepository(Courier::class)->findCouriersByFolder($loopFolder);

                foreach ($loopCouriers as $loopCourier)
                    $up_couriers[] = $loopCourier;
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.closure';
    }
}
