<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\PenaltyRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: PenaltyRepository::class)]
#[ORM\Table(name: "lucca_minute_penalty")]
class Penalty implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    /** TYPE constants */
    public const NATURE_AGGRAVATED = 'choice.nature.aggravated';
    public const NATURE_UNCHANGED = 'choice.nature.unchanged';
    public const NATURE_REGULARIZED = 'choice.nature.regularized';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateFolder = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $preparedBy;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    private ?string $nature = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountPenalty = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateStart = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateEnd = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Pénalité';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFolder(): ?DateTime
    {
        return $this->dateFolder;
    }

    public function setDateFolder(?DateTime $dateFolder): self
    {
        $this->dateFolder = $dateFolder;

        return $this;
    }

    public function getPreparedBy(): string
    {
        return $this->preparedBy;
    }

    public function setPreparedBy(string $preparedBy): self
    {
        $this->preparedBy = $preparedBy;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(?string $nature): self
    {
        $this->nature = $nature;

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

    public function getDateStart(): ?DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(?DateTime $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?DateTime $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }
}
