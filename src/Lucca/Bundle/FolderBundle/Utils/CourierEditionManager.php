<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Utils;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier;
use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierEdition;
use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierHumanEdition;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Human;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

/**
 * Class CourierEditionManager
 *
 * @package Lucca\Bundle\MinuteBundle\Utils
 * @author Térence <terence@numeric-wave.tech>
 */
class CourierEditionManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * CourierEditionManager constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Manage all editions after a submission form
     *
     * @param Courier $p_courier
     * @return Courier
     */
    public function manageEditionsOnFormSubmission(Courier $p_courier)
    {
        $editions = $this->em->getRepository('LuccaMinuteBundle:CourierHumanEdition')->findBy(array(
            'courier' => $p_courier
        ));

        if ($p_courier->getHumansEditions() === null || $p_courier->getHumansEditions()->count() === 0) {
            $this->createEditions($p_courier);
            return $p_courier;
        }

        /** @var Human $humans linked to Folder */
        $humans = $this->selectHumans($p_courier->getFolder());

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
            if (!$flagEditionExist)
                $this->createOneHumanEdition($p_courier, $human);
        }

        /**  If an edition stay after loops - then a human has been deleted then delete all rest editions */
        if (sizeof($editions) > 0) {
            foreach ($editions as $edition)
                $this->removeEdition($p_courier, $edition);
        }

        /** 2 - Manage edition for judicial and ddtm courier */
        if (!$p_courier->getEdition()) {
            $edition = new CourierEdition();
            $p_courier->setEdition($edition);
        }

        return $p_courier;
    }

    /**
     * Create all editions needed
     * @param Courier $p_courier
     * @return Courier
     */
    public function createEditions(Courier $p_courier)
    {
        /** @var Human $humans linked to Folder */
        $humans = $this->selectHumans($p_courier->getFolder());

        /** 1 - Loop for Humans defined in Minute list */
        foreach ($humans as $human)
            $this->createOneHumanEdition($p_courier, $human);

        /** 2 - Manage edition for judicial and ddtm courier */
        $edition = new CourierEdition();
        $p_courier->setEdition($edition);

        try {
            $this->em->persist($edition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when created Courier Edition - ' . $ORMException->getMessage();
        }

        return $p_courier;
    }

    /**
     * Create one Edition and linked this to Control
     *
     * @param Courier $p_courier
     * @param Human $p_human
     * @return Courier
     */
    private function createOneHumanEdition(Courier $p_courier, Human $p_human)
    {
        $edition = new CourierHumanEdition();
        $p_courier->addHumansEdition($edition);
        $edition->setHuman($p_human);

        try {
            $this->em->persist($edition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when created Courier Human Edition - ' . $ORMException->getMessage();
        }

        return $p_courier;
    }

    /**
     * Select Humans to create HumanEdition
     *
     * @param Folder $p_folder
     * @return ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    private function selectHumans(Folder $p_folder)
    {
        /** If Obstacle folder - then select only humans in frame 3 - else humans on frame 1 */
        if ($p_folder->getNature() === Folder::NATURE_OBSTACLE)
            return new ArrayCollection(array_merge(
                $p_folder->getHumansByMinute()->toArray(),
                $p_folder->getHumansByFolder()->toArray()
            ));

        return $p_folder->getMinute()->getHumans();
    }

    /**
     * Purge empty editions on Control entity
     *
     * @param Courier $courier
     * @return Courier $courier
     */
    public function purgeEditions(Courier $courier)
    {
        /** Loop for Editions based on Humans */
        foreach ($courier->getHumansEditions() as $edition) {
            /**  If no customization on Edition - delete it */
            if (!$edition->getLetterOffenderEdited())
                $this->removeOneHumanEdition($courier, $edition);
        }

        $edition = $courier->getEdition();
        if ($edition) {
            /**  If no customization on Edition - delete it */
            if (!$edition->getJudicialEdited() && !$edition->getDdtmEdited())
                $this->removeEdition($courier, $edition);
        }

        return $courier;
    }

    /**
     * Remove one Edition of Control
     *
     * @param Courier $p_courier
     * @param CourierEdition $p_courierEdition
     * @return Courier
     */
    private function removeEdition(Courier $p_courier, CourierEdition $p_courierEdition)
    {
        $p_courier->setEdition(null);

        try {
            $this->em->remove($p_courierEdition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when remove Courier Edition - ' . $ORMException->getMessage();
        }

        return $p_courier;
    }

    /**
     * Remove one Human Edition of Control
     *
     * @param Courier $p_courier
     * @param CourierHumanEdition $p_courierHumanEdition
     * @return Courier
     */
    private function removeOneHumanEdition(Courier $p_courier, CourierHumanEdition $p_courierHumanEdition)
    {
        $p_courier->removeHumansEdition($p_courierHumanEdition);

        try {
            $this->em->remove($p_courierHumanEdition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when remove Courier Human Edition - ' . $ORMException->getMessage();
        }

        return $p_courier;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.courier_human_edition';
    }
}
