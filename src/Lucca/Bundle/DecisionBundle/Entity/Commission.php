<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Commission
 *
 * @ORM\Table(name="lucca_minute_commission")
 * @ORM\Entity(repositoryClass="Lucca\Bundle\DecisionBundle\Repository\CommissionRepository")
 *
 * @package Lucca\Bundle\DecisionBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Commission implements LogInterface
{
    use TimestampableTrait;

    /** STATUS constants */
    public const STATUS_RELAXED = 'choice.status_deci.relaxed';
    public const STATUS_GUILTY = 'choice.status_deci.guilty';
    public const STATUS_GUILTY_EXEMPT = 'choice.status_deci.guilty_exempt';
    public const STATUS_GUILTY_RESTITUTION = 'choice.status_deci.guilty_restitution';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: "dateHearing", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateHearing = null;

    #[ORM\Column(name: "dateAdjournment", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateAdjournment = null;

    #[ORM\Column(name: "dateDeliberation", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateDeliberation = null;

    #[ORM\Column(name: "amountFine", type: Types::INTEGER, nullable: true)]
    #[Assert\Type(type: "integer", message: "constraint.type")]
    private ?int $amountFine = null;

    #[ORM\Column(name: "dateJudicialDesision", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateJudicialDesision = null;

    #[ORM\Column(name: "statusDecision", type: Types::STRING, length: 40, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    private ?string $statusDecision = null;

    #[ORM\Column(name: "amountPenalty", type: Types::INTEGER, nullable: true)]
    #[Assert\Type(type: "integer", message: "constraint.type")]
    private ?int $amountPenalty = null;

    #[ORM\Column(name: "dateExecution", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateExecution = null;

    #[ORM\Column(name: "restitution", type: Types::TEXT, nullable: true)]
    private ?string $restitution = null;

    #[ORM\Column(name: "dateStartPenality", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateStartPenality = null;


    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Automation for Date Start Penalty
     *
     * @return bool|\DateTime
     * @throws \DateMalformedStringException
     */
    public function autoDateStartPenalty(): \DateTime|bool
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
     * @return int|null
     */
    public function getId(): ?int
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
    public function setDateHearing(\DateTime $dateHearing): static
    {
        $this->dateHearing = $dateHearing;

        return $this;
    }

    /**
     * Get dateHearing
     *
     * @return \DateTime
     */
    public function getDateHearing(): ?\DateTime
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
    public function setDateAdjournment($dateAdjournment): static
    {
        $this->dateAdjournment = $dateAdjournment;

        return $this;
    }

    /**
     * Get dateAdjournment
     *
     * @return \DateTime
     */
    public function getDateAdjournment(): ?\DateTime
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
    public function setDateDeliberation($dateDeliberation): static
    {
        $this->dateDeliberation = $dateDeliberation;

        return $this;
    }

    /**
     * Get dateDeliberation
     *
     * @return \DateTime
     */
    public function getDateDeliberation(): ?\DateTime
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
    public function setAmountFine($amountFine): static
    {
        $this->amountFine = $amountFine;

        return $this;
    }

    /**
     * Get amountFine
     *
     * @return integer
     */
    public function getAmountFine(): ?int
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
    public function setDateJudicialDesision($dateJudicialDesision): static
    {
        $this->dateJudicialDesision = $dateJudicialDesision;

        return $this;
    }

    /**
     * Get dateJudicialDesision
     *
     * @return \DateTime
     */
    public function getDateJudicialDesision(): ?\DateTime
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
    public function setStatusDecision(string $statusDecision): static
    {
        $this->statusDecision = $statusDecision;

        return $this;
    }

    /**
     * Get statusDecision
     *
     * @return string
     */
    public function getStatusDecision(): ?string
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
    public function setAmountPenalty($amountPenalty): static
    {
        $this->amountPenalty = $amountPenalty;

        return $this;
    }

    /**
     * Get amountPenalty
     *
     * @return integer
     */
    public function getAmountPenalty(): ?int
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
    public function setDateExecution($dateExecution): static
    {
        $this->dateExecution = $dateExecution;

        return $this;
    }

    /**
     * Get dateExecution
     *
     * @return \DateTime
     */
    public function getDateExecution(): ?\DateTime
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
    public function setRestitution(string $restitution): static
    {
        $this->restitution = $restitution;

        return $this;
    }

    /**
     * Get restitution
     *
     * @return string
     */
    public function getRestitution(): ?string
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
    public function setDateStartPenality(\DateTime $dateStartPenality): static
    {
        $this->dateStartPenality = $dateStartPenality;

        return $this;
    }

    /**
     * Get dateStartPenality
     *
     * @return \DateTime
     */
    public function getDateStartPenality(): ?\DateTime
    {
        return $this->dateStartPenality;
    }
}
