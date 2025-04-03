<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\PenaltyAppealRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: PenaltyAppealRepository::class)]
#[ORM\Table(name: "lucca_minute_penalty_appeal")]
class PenaltyAppeal implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $juridiction;

    #[ORM\Column(nullable: true)]
    #[Assert\Type("\DateTimeInterface")]
    private ?DateTime $dateDecision = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $kindDecision = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Appel des pénalités';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJuridiction(): string
    {
        return $this->juridiction;
    }

    public function setJuridiction(string $juridiction): self
    {
        $this->juridiction = $juridiction;

        return $this;
    }

    public function getDateDecision(): ?DateTime
    {
        return $this->dateDecision;
    }

    public function setDateDecision(?DateTime $dateDecision): self
    {
        $this->dateDecision = $dateDecision;

        return $this;
    }

    public function getKindDecision(): ?string
    {
        return $this->kindDecision;
    }

    public function setKindDecision(?string $kindDecision): self
    {
        $this->kindDecision = $kindDecision;

        return $this;
    }
}
