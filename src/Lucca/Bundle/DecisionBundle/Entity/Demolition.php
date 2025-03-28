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
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\DemolitionRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: DemolitionRepository::class)]
#[ORM\Table(name: "lucca_minute_demolition")]
class Demolition implements LoggableInterface
{
    use TimestampableTrait;

    /** RESULT constants */
    const RESULT_REALIZED = 'choice.result.realized';
    const RESULT_REPORTED = 'choice.result.reported';
    const RESULT_CANCELLED = 'choice.result.cancelled';
    const RESULT_WAITING = 'choice.result.waiting';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id;

    #[ORM\OneToOne(targetEntity: Decision::class, inversedBy: "demolition")]
    #[ORM\JoinColumn(nullable: false)]
    private Decision $decision;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $company = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountCompany = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type("\DateTimeInterface")]
    private ?DateTime $dateDemolition = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $bailif = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountBailif = null;

    #[ORM\JoinTable(name: "lucca_minute_demolition_linked_profession")]
    #[ORM\JoinColumn(name: "demolition_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "profession_id", referencedColumnName: "id")]
    #[ORM\ManyToMany(targetEntity: Profession::class, cascade: ["persist", "remove"])]
    private Collection $professions;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 30, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $result = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->professions = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Démolition';
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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getAmountCompany(): ?int
    {
        return $this->amountCompany;
    }

    public function setAmountCompany(?int $amountCompany): self
    {
        $this->amountCompany = $amountCompany;

        return $this;
    }

    public function getDateDemolition(): ?DateTime
    {
        return $this->dateDemolition;
    }

    public function setDateDemolition(?DateTime $dateDemolition): self
    {
        $this->dateDemolition = $dateDemolition;

        return $this;
    }

    public function getBailif(): ?string
    {
        return $this->bailif;
    }

    public function setBailif(?string $bailif): self
    {
        $this->bailif = $bailif;

        return $this;
    }

    public function getAmountBailif(): ?int
    {
        return $this->amountBailif;
    }

    public function setAmountBailif(?int $amountBailif): self
    {
        $this->amountBailif = $amountBailif;

        return $this;
    }

    public function getProfessions(): Collection
    {
        return $this->professions;
    }

    public function addProfession(Profession $profession): self
    {
        if (!$this->professions->contains($profession)) {
            $this->professions->add($profession);
        }

        return $this;
    }

    public function removeProfession(Profession $profession): self
    {
        $this->professions->removeElement($profession);

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

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): self
    {
        $this->result = $result;

        return $this;
    }
}
