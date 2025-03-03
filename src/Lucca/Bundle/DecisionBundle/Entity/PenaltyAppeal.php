<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\PenaltyAppealRepository;
use Lucca\Bundle\LogBundle\Entity\LogInterface;

#[ORM\Entity(repositoryClass: PenaltyAppealRepository::class)]
#[ORM\Table(name: "lucca_minute_penalty_appeal")]
class PenaltyAppeal implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $juridiction;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateDecision = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $kindDecision = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function getLogName(): string
    {
        return 'Appel des pénalités';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setJuridiction(string $juridiction): static
    {
        $this->juridiction = $juridiction;
        return $this;
    }

    public function getJuridiction(): string
    {
        return $this->juridiction;
    }

    public function setDateDecision(?\DateTime $dateDecision): static
    {
        $this->dateDecision = $dateDecision;
        return $this;
    }

    public function getDateDecision(): ?\DateTime
    {
        return $this->dateDecision;
    }

    public function setKindDecision(?string $kindDecision): static
    {
        $this->kindDecision = $kindDecision;
        return $this;
    }

    public function getKindDecision(): ?string
    {
        return $this->kindDecision;
    }
}