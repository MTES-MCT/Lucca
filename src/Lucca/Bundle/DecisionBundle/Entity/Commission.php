<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

use DateTime, DateMalformedStringException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\CommissionRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: CommissionRepository::class)]
#[ORM\Table(name: 'lucca_minute_commission')]
class Commission implements LoggableInterface
{
    use TimestampableTrait;

    /** STATUS constants */
    public const STATUS_RELAXED = 'choice.status_deci.relaxed';
    public const STATUS_GUILTY = 'choice.status_deci.guilty';
    public const STATUS_GUILTY_EXEMPT = 'choice.status_deci.guilty_exempt';
    public const STATUS_GUILTY_RESTITUTION = 'choice.status_deci.guilty_restitution';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateHearing = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateAdjournment = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateDeliberation = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "integer", message: "constraint.type")]
    private ?int $amountFine = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateJudicialDesision = null;

    #[ORM\Column(length: 40, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    private ?string $statusDecision = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "integer", message: "constraint.type")]
    private ?int $amountPenalty = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateExecution = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $restitution = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateStartPenality = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Automation for Date Start Penalty
     *
     * @throws DateMalformedStringException
     */
    public function autoDateStartPenalty(): DateTime|bool
    {
        if ($this->getDateExecution()) {
            $dateStart = new DateTime($this->getDateExecution()->format('Y-m-d H:i:s'));
            $dateStart->modify('+1 days');
            $this->setDateStartPenality($dateStart);

            return $dateStart;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Commission';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHearing(): ?DateTime
    {
        return $this->dateHearing;
    }

    public function setDateHearing(?DateTime $dateHearing): self
    {
        $this->dateHearing = $dateHearing;

        return $this;
    }

    public function getDateAdjournment(): ?DateTime
    {
        return $this->dateAdjournment;
    }

    public function setDateAdjournment(?DateTime $dateAdjournment): self
    {
        $this->dateAdjournment = $dateAdjournment;

        return $this;
    }

    public function getDateDeliberation(): ?DateTime
    {
        return $this->dateDeliberation;
    }

    public function setDateDeliberation(?DateTime $dateDeliberation): self
    {
        $this->dateDeliberation = $dateDeliberation;

        return $this;
    }

    public function getAmountFine(): ?int
    {
        return $this->amountFine;
    }

    public function setAmountFine(?int $amountFine): self
    {
        $this->amountFine = $amountFine;

        return $this;
    }

    public function getDateJudicialDesision(): ?DateTime
    {
        return $this->dateJudicialDesision;
    }

    public function setDateJudicialDesision(?DateTime $dateJudicialDesision): self
    {
        $this->dateJudicialDesision = $dateJudicialDesision;

        return $this;
    }

    public function getStatusDecision(): ?string
    {
        return $this->statusDecision;
    }

    public function setStatusDecision(?string $statusDecision): self
    {
        $this->statusDecision = $statusDecision;

        return $this;
    }

    public function getAmountPenalty(): ?int
    {
        return $this->amountPenalty;
    }

    public function setAmountPenalty(?int $amountPenalty): self
    {
        $this->amountPenalty = $amountPenalty;

        return $this;
    }

    public function getDateExecution(): ?DateTime
    {
        return $this->dateExecution;
    }

    public function setDateExecution(?DateTime $dateExecution): self
    {
        $this->dateExecution = $dateExecution;

        return $this;
    }

    public function getRestitution(): ?string
    {
        return $this->restitution;
    }

    public function setRestitution(?string $restitution): self
    {
        $this->restitution = $restitution;

        return $this;
    }

    public function getDateStartPenality(): ?DateTime
    {
        return $this->dateStartPenality;
    }

    public function setDateStartPenality(?DateTime $dateStartPenality): self
    {
        $this->dateStartPenality = $dateStartPenality;

        return $this;
    }
}
