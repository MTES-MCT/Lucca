<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

USE DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\ExpulsionRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: ExpulsionRepository::class)]
#[ORM\Table(name: "lucca_minute_expulsion")]
class Expulsion implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Decision::class, inversedBy: "expulsion")]
    #[ORM\JoinColumn(nullable: false)]
    private Decision $decision;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $lawFirm = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountDelivrery = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type("\DateTimeInterface")]
    private ?DateTime $dateHearing = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type("\DateTimeInterface")]
    private ?DateTime $dateAdjournment = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type("\DateTimeInterface")]
    private ?DateTime $dateDeliberation = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type("\DateTimeInterface")]
    private ?DateTime $dateJudicialDesision = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $statusDecision = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Expulsion';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDecision(): Decision
    {
        return $this->decision;
    }

    public function setDecision(Decision $decision): self
    {
        $this->decision = $decision;

        return $this;
    }

    public function getLawFirm(): ?string
    {
        return $this->lawFirm;
    }

    public function setLawFirm(?string $lawFirm): self
    {
        $this->lawFirm = $lawFirm;

        return $this;
    }

    public function getAmountDelivrery(): ?int
    {
        return $this->amountDelivrery;
    }

    public function setAmountDelivrery(?int $amountDelivrery): self
    {
        $this->amountDelivrery = $amountDelivrery;

        return $this;
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
