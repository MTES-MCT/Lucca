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
 * Penalty
 *
 * @ORM\Table(name="lucca_minute_penalty")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\PenaltyRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Penalty implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** TYPE constants */
    const NATURE_AGGRAVATED = 'choice.nature.aggravated';
    const NATURE_UNCHANGED = 'choice.nature.unchanged';
    const NATURE_REGULARIZED = 'choice.nature.regularized';

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
     * @ORM\Column(name="dateFolder", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateFolder;

    /**
     * @var string
     *
     * @ORM\Column(name="preparedBy", type="string", length=50)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $preparedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="nature", type="string", length=30, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $nature;

    /**
     * @var int
     *
     * @ORM\Column(name="amountPenalty", type="integer", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $amountPenalty;

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

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Pénalité';
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
     * Set dateFolder
     *
     * @param \DateTime $dateFolder
     *
     * @return Penalty
     */
    public function setDateFolder($dateFolder)
    {
        $this->dateFolder = $dateFolder;

        return $this;
    }

    /**
     * Get dateFolder
     *
     * @return \DateTime
     */
    public function getDateFolder()
    {
        return $this->dateFolder;
    }

    /**
     * Set preparedBy
     *
     * @param string $preparedBy
     *
     * @return Penalty
     */
    public function setPreparedBy($preparedBy)
    {
        $this->preparedBy = $preparedBy;

        return $this;
    }

    /**
     * Get preparedBy
     *
     * @return string
     */
    public function getPreparedBy()
    {
        return $this->preparedBy;
    }

    /**
     * Set amountPenalty
     *
     * @param integer $amountPenalty
     *
     * @return Penalty
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

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return Penalty
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
     * @return Penalty
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
     * Set nature
     *
     * @param string $nature
     *
     * @return Penalty
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature
     *
     * @return string
     */
    public function getNature()
    {
        return $this->nature;
    }
}
