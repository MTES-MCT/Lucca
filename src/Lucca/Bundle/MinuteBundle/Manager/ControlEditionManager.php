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
use Doctrine\ORM\Exception\ORMException;

use Lucca\Bundle\MinuteBundle\Entity\{Control, ControlEdition, Human};

readonly class ControlEditionManager
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    /**
     * Manage ControlEdition after a form submission
     */
    public function manageEditionsOnFormSubmission(Control $control): Control
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
            if (!$flagEditionExist) {
                $this->createOneEdition($control, $human);
            }
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
            if (!$flagEditionExist) {
                $this->createOneEdition($control, $human);
            }
        }

        /**  If an edition stay after loops - then a human has been deleted then delete all rest editions */
        if (sizeof($editions) > 0) {
            foreach ($editions as $edition) {
                $this->removeOneEdition($control, $edition);
            }
        }

        return $control;
    }

    /**
     * Create all editions required for a specific Control
     * One ControlEdition per Human
     */
    public function createEditions(Control $control): void
    {
        /** 1 - Loop for Humans defined in Minute list */
        foreach ($control->getHumansByMinute() as $human) {
            $this->createOneEdition($control, $human);
        }

        /** 2 - Loop for Humans defined in Control */
        foreach ($control->getHumansByControl() as $human) {
            $this->createOneEdition($control, $human);
        }
    }

    /**
     * Purge empty editions on Control entity
     */
    public function purgeEditions(Control $control): Control
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
     */
    private function createOneEdition(Control $control, Human $human): Control
    {
        $edition = new ControlEdition();
        $control->addEdition($edition);
        $edition->setHuman($human);

        try {
            $this->em->persist($edition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when created Control Edition - ' . $ORMException->getMessage();
        }

        return $control;
    }

    /**
     * Remove on Edition of Control
     */
    private function removeOneEdition(Control $control, ControlEdition $controlEdition): Control
    {
        $control->removeEdition($controlEdition);

        try {
            $this->em->remove($controlEdition);
        } catch (ORMException $ORMException) {
            echo 'New exception thrown when remove Control Edition - ' . $ORMException->getMessage();
        }

        return $control;
    }

    public function getName(): string
    {
        return 'lucca.manager.control_edition';
    }
}
