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
 * Expulsion
 *
 * @ORM\Table(name="lucca_minute_expulsion")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\ExpulsionRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Expulsion implements LogInterface
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
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Decision", inversedBy="expulsion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $decision;

    /**
     * @var string
     *
     * @ORM\Column(name="lawFirm", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $lawFirm;

    /**
     * @var int
     *
     * @ORM\Column(name="amountDelivrery", type="integer", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $amountDelivrery;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateHearing", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateHearing;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdjournment", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateAdjournment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDeliberation", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateDeliberation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateJudicialDesision", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateJudicialDesision;

    /**
     * @var string
     *
     * @ORM\Column(name="statusDecision", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $statusDecision;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Expulsion';
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
     * Set lawFirm
     *
     * @param string $lawFirm
     *
     * @return Expulsion
     */
    public function setLawFirm($lawFirm)
    {
        $this->lawFirm = $lawFirm;

        return $this;
    }

    /**
     * Get lawFirm
     *
     * @return string
     */
    public function getLawFirm()
    {
        return $this->lawFirm;
    }

    /**
     * Set amountDelivrery
     *
     * @param integer $amountDelivrery
     *
     * @return Expulsion
     */
    public function setAmountDelivrery($amountDelivrery)
    {
        $this->amountDelivrery = $amountDelivrery;

        return $this;
    }

    /**
     * Get amountDelivrery
     *
     * @return integer
     */
    public function getAmountDelivrery()
    {
        return $this->amountDelivrery;
    }

    /**
     * Set dateHearing
     *
     * @param \DateTime $dateHearing
     *
     * @return Expulsion
     */
    public function setDateHearing($dateHearing)
    {
        $this->dateHearing = $dateHearing;

        return $this;
    }

    /**
     * Get dateHearing
     *
     * @return \DateTime
     */
    public function getDateHearing()
    {
        return $this->dateHearing;
    }

    /**
     * Set dateAdjournment
     *
     * @param \DateTime $dateAdjournment
     *
     * @return Expulsion
     */
    public function setDateAdjournment($dateAdjournment)
    {
        $this->dateAdjournment = $dateAdjournment;

        return $this;
    }

    /**
     * Get dateAdjournment
     *
     * @return \DateTime
     */
    public function getDateAdjournment()
    {
        return $this->dateAdjournment;
    }

    /**
     * Set dateDeliberation
     *
     * @param \DateTime $dateDeliberation
     *
     * @return Expulsion
     */
    public function setDateDeliberation($dateDeliberation)
    {
        $this->dateDeliberation = $dateDeliberation;

        return $this;
    }

    /**
     * Get dateDeliberation
     *
     * @return \DateTime
     */
    public function getDateDeliberation()
    {
        return $this->dateDeliberation;
    }

    /**
     * Set dateJudicialDesision
     *
     * @param \DateTime $dateJudicialDesision
     *
     * @return Expulsion
     */
    public function setDateJudicialDesision($dateJudicialDesision)
    {
        $this->dateJudicialDesision = $dateJudicialDesision;

        return $this;
    }

    /**
     * Get dateJudicialDesision
     *
     * @return \DateTime
     */
    public function getDateJudicialDesision()
    {
        return $this->dateJudicialDesision;
    }

    /**
     * Set statusDecision
     *
     * @param string $statusDecision
     *
     * @return Expulsion
     */
    public function setStatusDecision($statusDecision)
    {
        $this->statusDecision = $statusDecision;

        return $this;
    }

    /**
     * Get statusDecision
     *
     * @return string
     */
    public function getStatusDecision()
    {
        return $this->statusDecision;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Expulsion
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set decision
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision $decision
     *
     * @return Expulsion
     */
    public function setDecision(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision $decision)
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * Get decision
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision
     */
    public function getDecision()
    {
        return $this->decision;
    }
}
