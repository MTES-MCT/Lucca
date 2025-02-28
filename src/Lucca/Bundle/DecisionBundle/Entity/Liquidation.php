<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Liquidation
 *
 * @ORM\Table(name="lucca_minute_liquidation")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\LiquidationRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Liquidation implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStart", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnd", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="amountPenalty", type="integer", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $amountPenalty;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Liquidation';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return Liquidation
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return Liquidation
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set amountPenalty
     *
     * @param integer $amountPenalty
     *
     * @return Liquidation
     */
    public function setAmountPenalty($amountPenalty)
    {
        $this->amountPenalty = $amountPenalty;

        return $this;
    }

    /**
     * Get amountPenalty
     *
     * @return integer
     */
    public function getAmountPenalty()
    {
        return $this->amountPenalty;
    }
}
