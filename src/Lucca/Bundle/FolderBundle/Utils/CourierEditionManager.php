<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Utils;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

use Lucca\Bundle\FolderBundle\Entity\{Courier, CourierEdition, CourierHumanEdition, Folder};
use Lucca\Bundle\MinuteBundle\Entity\Human;

readonly class CourierEditionManager
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    /**
     * Manage all editions after a submission form
     */
    public function manageEditionsOnFormSubmission(Courier $courier): Courier
    {
        $editions = $this->em->getRepository(CourierHumanEdition::class)->findBy([

            'courier' => $courier
        ]);

        if ($courier->getHumansEditions() === null || $courier->getHumansEditions()->count() === 0) {
            $this->createEditions($courier);

            return $courier;
        }

        /** @var Human $humans linked to Folder */
        $humans = $this->selectHumans($courier->getFolder());

        /** 1 - Loop for Humans defined in Minute list */
        foreach ($humans as $human) {
            $flagEditionExist = false;

            /**  If an edition exist with an human */
            foreach ($editions as $ind => $edition) {
                if ($edition->getHuman() === $human) {
                    unset($editions[$ind]);
                    $flagEditionExist = true;
                }
            }

            /**  If an edition does not exist then create one */
            if (!$flagEditionExist) {
                $this->createOneHumanEdition($courier, $human);
            }
        }

        /**  If an edition stay after loops - then a human has been deleted then delete all rest editions */
        if (sizeof($editions) > 0) {
            foreach ($editions as $edition) {
                $this->removeEdition($courier, $edition);
            }
        }

        /** 2 - Manage edition for judicial and ddtm courier */
        if (!$courier->getEdition()) {
            $edition = new CourierEdition();
            $courier->setEdition($edition);
        }

        return $courier;
    }

    /**
     * Create all editions needed
     */
    public function createEditions(Courier $courier): Courier
    {
        /** @var Human $humans linked to Folder */
        $humans = $this->selectHumans($courier->getFolder());

        /** 1 - Loop for Humans defined in Minute list */
        foreach ($humans as $human) {
            $this->createOneHumanEdition($courier, $human);
        }

        /** 2 - Manage edition for judicial and ddtm courier */
        $edition = new CourierEdition();
        $courier->setEdition($edition);

        try {
            $this->em->persist($edition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when created Courier Edition - ' . $ORMException->getMessage();
        }

        return $courier;
    }

    /**
     * Create one Edition and linked this to Control
     */
    private function createOneHumanEdition(Courier $courier, Human $human): Courier
    {
        $edition = new CourierHumanEdition();
        $courier->addHumansEdition($edition);
        $edition->setHuman($human);

        try {
            $this->em->persist($edition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when created Courier Human Edition - ' . $ORMException->getMessage();
        }

        return $courier;
    }

    /**
     * Select Humans to create HumanEdition
     */
    private function selectHumans(Folder $folder): Collection
    {
        /** If Obstacle folder - then select only humans in frame 3 - else humans on frame 1 */
        if ($folder->getNature() === Folder::NATURE_OBSTACLE)
            return new ArrayCollection(array_merge(
                $folder->getHumansByMinute()->toArray(),
                $folder->getHumansByFolder()->toArray()
            ));

        return $folder->getMinute()->getHumans();
    }

    /**
     * Purge empty editions on Control entity
     */
    public function purgeEditions(Courier $courier): Courier
    {
        /** Loop for Editions based on Humans */
        foreach ($courier->getHumansEditions() as $edition) {
            /**  If no customization on Edition - delete it */
            if (!$edition->getLetterOffenderEdited()) {
                $this->removeOneHumanEdition($courier, $edition);
            }
        }

        $edition = $courier->getEdition();
        if ($edition) {
            /**  If no customization on Edition - delete it */
            if (!$edition->getJudicialEdited() && !$edition->getDdtmEdited()) {
                $this->removeEdition($courier, $edition);
            }
        }

        return $courier;
    }

    /**
     * Remove one Edition of Control
     */
    private function removeEdition(Courier $courier, CourierEdition $courierEdition): Courier
    {
        $courier->setEdition(null);

        try {
            $this->em->remove($courierEdition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when remove Courier Edition - ' . $ORMException->getMessage();
        }

        return $courier;
    }

    /**
     * Remove one Human Edition of Control
     */
    private function removeOneHumanEdition(Courier $courier, CourierHumanEdition $courierHumanEdition): Courier
    {
        $courier->removeHumansEdition($courierHumanEdition);

        try {
            $this->em->remove($courierHumanEdition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when remove Courier Human Edition - ' . $ORMException->getMessage();
        }

        return $courier;
    }
}
