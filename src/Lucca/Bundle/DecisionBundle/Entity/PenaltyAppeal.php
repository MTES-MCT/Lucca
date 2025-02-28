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
 * PenaltyAppeal
 *
 * @ORM\Table(name="lucca_minute_penalty_appeal")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\PenaltyAppealRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class PenaltyAppeal implements LogInterface
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
     * @var string
     *
     * @ORM\Column(name="juridiction", type="string", length=50)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $juridiction;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDecision", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateDecision;

    /**
     * @var string
     *
     * @ORM\Column(name="kindDecision", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $kindDecision;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Appel des pénalités';
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
     * Set juridiction
     *
     * @param string $juridiction
     *
     * @return PenaltyAppeal
     */
    public function setJuridiction($juridiction)
    {
        $this->juridiction = $juridiction;

        return $this;
    }

    /**
     * Get juridiction
     *
     * @return string
     */
    public function getJuridiction()
    {
        return $this->juridiction;
    }

    /**
     * Set dateDecision
     *
     * @param \DateTime $dateDecision
     *
     * @return PenaltyAppeal
     */
    public function setDateDecision($dateDecision)
    {
        $this->dateDecision = $dateDecision;

        return $this;
    }

    /**
     * Get dateDecision
     *
     * @return \DateTime
     */
    public function getDateDecision()
    {
        return $this->dateDecision;
    }

    /**
     * Set kindDecision
     *
     * @param string $kindDecision
     *
     * @return PenaltyAppeal
     */
    public function setKindDecision($kindDecision)
    {
        $this->kindDecision = $kindDecision;

        return $this;
    }

    /**
     * Get kindDecision
     *
     * @return string
     */
    public function getKindDecision()
    {
        return $this->kindDecision;
    }
}
