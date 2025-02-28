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
 * Commission
 *
 * @ORM\Table(name="lucca_minute_commission")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\CommissionRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Commission implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** STATUS constants */
    const STATUS_RELAXED = 'choice.status_deci.relaxed';
    const STATUS_GUILTY = 'choice.status_deci.guilty';
    const STATUS_GUILTY_EXEMPT = 'choice.status_deci.guilty_exempt';
    const STATUS_GUILTY_RESTITUTION = 'choice.status_deci.guilty_restitution';

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
     * @var int
     *
     * @ORM\Column(name="amountFine", type="integer", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $amountFine;

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
     * @ORM\Column(name="statusDecision", type="string", length=40, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $statusDecision;

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
     * @ORM\Column(name="dateExecution", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateExecution;

    /**
     * @var string
     *
     * @ORM\Column(name="restitution", type="text", nullable=true)
     */
    private $restitution;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStartPenality", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateStartPenality;


    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Automation for Date Start Penalty
     *
     * @return bool|\DateTime
     */
    public function autoDateStartPenalty()
    {
        if ($this->getDateExecution()) {
            $dateStart = new \DateTime($this->getDateExecution()->format('Y-m-d H:i:s'));
            $dateStart->modify('+1 days');
            $this->setDateStartPenality($dateStart);
            return $dateStart;
        }
        return false;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Commission';
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
     * Set dateHearing
     *
     * @param \DateTime $dateHearing
     *
     * @return Commission
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
     * @return Commission
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
     * @return Commission
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
     * Set amountFine
     *
     * @param integer $amountFine
     *
     * @return Commission
     */
    public function setAmountFine($amountFine)
    {
        $this->amountFine = $amountFine;

        return $this;
    }

    /**
     * Get amountFine
     *
     * @return integer
     */
    public function getAmountFine()
    {
        return $this->amountFine;
    }

    /**
     * Set dateJudicialDesision
     *
     * @param \DateTime $dateJudicialDesision
     *
     * @return Commission
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
     * @return Commission
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
     * Set amountPenalty
     *
     * @param integer $amountPenalty
     *
     * @return Commission
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
     * Set dateExecution
     *
     * @param \DateTime $dateExecution
     *
     * @return Commission
     */
    public function setDateExecution($dateExecution)
    {
        $this->dateExecution = $dateExecution;

        return $this;
    }

    /**
     * Get dateExecution
     *
     * @return \DateTime
     */
    public function getDateExecution()
    {
        return $this->dateExecution;
    }

    /**
     * Set restitution
     *
     * @param string $restitution
     *
     * @return Commission
     */
    public function setRestitution($restitution)
    {
        $this->restitution = $restitution;

        return $this;
    }

    /**
     * Get restitution
     *
     * @return string
     */
    public function getRestitution()
    {
        return $this->restitution;
    }

    /**
     * Set dateStartPenality
     *
     * @param \DateTime $dateStartPenality
     *
     * @return Commission
     */
    public function setDateStartPenality($dateStartPenality)
    {
        $this->dateStartPenality = $dateStartPenality;

        return $this;
    }

    /**
     * Get dateStartPenality
     *
     * @return \DateTime
     */
    public function getDateStartPenality()
    {
        return $this->dateStartPenality;
    }
}
