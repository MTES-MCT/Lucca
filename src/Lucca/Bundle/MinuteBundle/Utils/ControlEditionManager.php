<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Utils;

use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\ControlEdition;
use Lucca\Bundle\MinuteBundle\Entity\Human;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

/**
 * Class ControlEditionManager
 *
 * @package Lucca\Bundle\MinuteBundle\Utils
 * @author Térence <terence@numeric-wave.tech>
 */
class ControlEditionManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * ControlEditionManager constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Manage ControlEdition after a form submission
     *
     * @param Control $control
     * @return Control
     */
    public function manageEditionsOnFormSubmission(Control $control)
    {
        $editions = $control->getEditions();

        if ($control->getEditions() === null || $control->getEditions()->count() === 0) {
            $this->createEditions($control);
            return $control;
        }

        /** 1 - Loop for Humans defined in Minute list */
        foreach ($control->getHumansByMinute() as $human) {
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
                $this->createOneEdition($control, $human);
        }

        /** 2 - Loop for Humans defined in Control */
        foreach ($control->getHumansByControl() as $human) {
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
                $this->createOneEdition($control, $human);
        }

        /**  If an edition stay after loops - then a human has been deleted then delete all rest editions */
        if (sizeof($editions) > 0) {
            foreach ($editions as $edition)
                $this->removeOneEdition($control, $edition);
        }

        return $control;
    }

    /**
     * Create all editions required for a specific Control
     * One ControlEdition per Human
     *
     * @param Control $p_control
     */
    public function createEditions(Control $p_control)
    {
        /** 1 - Loop for Humans defined in Minute list */
        foreach ($p_control->getHumansByMinute() as $human) {
            $this->createOneEdition($p_control, $human);
        }

        /** 2 - Loop for Humans defined in Control */
        foreach ($p_control->getHumansByControl() as $human) {
            $this->createOneEdition($p_control, $human);
        }
    }

    /**
     * Purge empty editions on Control entity
     *
     * @param Control $control
     * @return Control
     */
    public function purgeEditions(Control $control)
    {
        /** Loop for Editions founded */
        foreach ($control->getEditions() as $edition) {
            /**  If no customization on Edition - delete it */
            if (!$edition->getConvocationEdited() && !$edition->getAccessEdited()) {
                $this->removeOneEdition($control, $edition);
            }
        }

        return $control;
    }

    /**
     * Create one Edition and linked this to Control
     *
     * @param Control $p_control
     * @param Human $p_human
     * @return Control
     */
    private function createOneEdition(Control $p_control, Human $p_human)
    {
        $edition = new ControlEdition();
        $p_control->addEdition($edition);
        $edition->setHuman($p_human);

        try {
            $this->em->persist($edition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when created Control Edition - ' . $ORMException->getMessage();
        }

        return $p_control;
    }

    /**
     * Remove on Edition of Control
     *
     * @param Control $p_control
     * @param ControlEdition $p_controlEdition
     * @return Control
     */
    private function removeOneEdition(Control $p_control, ControlEdition $p_controlEdition)
    {
        $p_control->removeEdition($p_controlEdition);

        try {
            $this->em->remove($p_controlEdition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when remove Control Edition - ' . $ORMException->getMessage();
        }

        return $p_control;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.control_edition';
    }
}
