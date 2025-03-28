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
use Lucca\Bundle\DecisionBundle\Repository\DecisionRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\ParameterBundle\Entity\Tribunal;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Entity(repositoryClass: DecisionRepository::class)]
#[ORM\Table(name: "lucca_minute_decision")]
class Decision implements LoggableInterface
{
    use TimestampableTrait;

    /** TYPE constants */
    public const STATUS_REGULARIZED = 'choice.status.regularized';
    public const STATUS_DEMOLITION = 'choice.status.demolition';
    public const STATUS_EXEC_OFFICE = 'choice.status.exec_office';
    public const STATUS_RELAXED = 'choice.status.relaxed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Minute::class, inversedBy: "decisions")]
    #[ORM\JoinColumn(nullable: false)]
    private Minute $minute;

    #[ORM\ManyToOne(targetEntity: Tribunal::class)]
    private ?Tribunal $tribunal = null;

    #[ORM\ManyToOne(targetEntity: Commission::class, cascade: ["persist", "remove"])]
    private ?Commission $tribunalCommission = null;

    #[ORM\Column]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private bool $appeal = false;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\ManyToOne(targetEntity: Commission::class, cascade: ["persist", "remove"])]
    private ?Commission $appealCommission = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private ?bool $cassationComplaint = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateAskCassation = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateAnswerCassation = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private ?bool $statusCassation = null;

    #[ORM\ManyToOne(targetEntity: Commission::class, cascade: ["persist", "remove"])]
    private ?Commission $cassationComission = null;

    #[ORM\Column(length: 35, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    private ?string $nameNewCassation = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateReferralEurope = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $answerEurope = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dataEurope = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountPenaltyDaily = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateStartRecovery = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?DateTime $dateNoticeDdtm = null;

    #[ORM\JoinTable(name: "lucca_minute_decision_linked_penalty")]
    #[ORM\JoinColumn(name: 'decision_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'penalty_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Penalty::class, cascade: ["persist", "remove"])]
    private Collection $penalties;

    #[ORM\JoinTable(name: "lucca_minute_decision_linked_liquidation")]
    #[ORM\JoinColumn(name: 'decision_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'liquidation_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Liquidation::class, cascade: ["persist", "remove"])]
    private Collection $liquidations;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $totalPenaltyRecovery = null;

    #[ORM\JoinTable(name: "lucca_minute_decision_linked_penalty_appeal")]
    #[ORM\JoinColumn(name: 'decision_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'penalty_appeal_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: PenaltyAppeal::class, cascade: ["persist", "remove"])]
    private Collection $appealPenalties;

    #[ORM\JoinTable(name: "lucca_minute_decision_linked_contradictory")]
    #[ORM\JoinColumn(name: 'decision_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'contradictory_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Contradictory::class, cascade: ["persist", "remove"])]
    private Collection $contradictories;

    #[ORM\OneToOne(targetEntity: Expulsion::class, mappedBy: "decision", cascade: ["persist", "remove"])]
    private ?Expulsion $expulsion = null;

    #[ORM\OneToOne(targetEntity: Demolition::class, mappedBy: "decision", cascade: ["persist", "remove"])]
    private ?Demolition $demolition = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->penalties = new ArrayCollection();
        $this->liquidations = new ArrayCollection();
        $this->appealPenalties = new ArrayCollection();
        $this->contradictories = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Décision';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinute(): Minute
    {
        return $this->minute;
    }

    public function setMinute(Minute $minute): self
    {
        $this->minute = $minute;

        return $this;
    }

    public function getTribunal(): ?Tribunal
    {
        return $this->tribunal;
    }

    public function setTribunal(?Tribunal $tribunal): self
    {
        $this->tribunal = $tribunal;

        return $this;
    }

    public function getTribunalCommission(): ?Commission
    {
        return $this->tribunalCommission;
    }

    public function setTribunalCommission(?Commission $tribunalCommission): self
    {
        $this->tribunalCommission = $tribunalCommission;

        return $this;
    }

    public function isAppeal(): bool
    {
        return $this->appeal;
    }

    public function setAppeal(bool $appeal): self
    {
        $this->appeal = $appeal;

        return $this;
    }

    public function getAppealCommission(): ?Commission
    {
        return $this->appealCommission;
    }

    public function setAppealCommission(?Commission $appealCommission): self
    {
        $this->appealCommission = $appealCommission;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function isCassationComplaint(): ?bool
    {
        return $this->cassationComplaint;
    }

    public function setCassationComplaint(?bool $cassationComplaint): self
    {
        $this->cassationComplaint = $cassationComplaint;

        return $this;
    }

    public function getDateAskCassation(): ?DateTime
    {
        return $this->dateAskCassation;
    }

    public function setDateAskCassation(?DateTime $dateAskCassation): self
    {
        $this->dateAskCassation = $dateAskCassation;

        return $this;
    }

    public function getDateAnswerCassation(): ?DateTime
    {
        return $this->dateAnswerCassation;
    }

    public function setDateAnswerCassation(?DateTime $dateAnswerCassation): self
    {
        $this->dateAnswerCassation = $dateAnswerCassation;

        return $this;
    }

    public function isStatusCassation(): ?bool
    {
        return $this->statusCassation;
    }

    public function setStatusCassation(?bool $statusCassation): self
    {
        $this->statusCassation = $statusCassation;

        return $this;
    }

    public function getCassationComission(): ?Commission
    {
        return $this->cassationComission;
    }

    public function setCassationComission(?Commission $cassationComission): self
    {
        $this->cassationComission = $cassationComission;

        return $this;
    }

    public function getNameNewCassation(): ?string
    {
        return $this->nameNewCassation;
    }

    public function setNameNewCassation(?string $nameNewCassation): self
    {
        $this->nameNewCassation = $nameNewCassation;

        return $this;
    }

    public function getDateReferralEurope(): ?DateTime
    {
        return $this->dateReferralEurope;
    }

    public function setDateReferralEurope(?DateTime $dateReferralEurope): self
    {
        $this->dateReferralEurope = $dateReferralEurope;

        return $this;
    }

    public function getAnswerEurope(): ?DateTime
    {
        return $this->answerEurope;
    }

    public function setAnswerEurope(?DateTime $answerEurope): self
    {
        $this->answerEurope = $answerEurope;

        return $this;
    }

    public function getDataEurope(): ?string
    {
        return $this->dataEurope;
    }

    public function setDataEurope(?string $dataEurope): self
    {
        $this->dataEurope = $dataEurope;

        return $this;
    }

    public function getAmountPenaltyDaily(): ?int
    {
        return $this->amountPenaltyDaily;
    }

    public function setAmountPenaltyDaily(?int $amountPenaltyDaily): self
    {
        $this->amountPenaltyDaily = $amountPenaltyDaily;

        return $this;
    }

    public function getDateStartRecovery(): ?DateTime
    {
        return $this->dateStartRecovery;
    }

    public function setDateStartRecovery(?DateTime $dateStartRecovery): self
    {
        $this->dateStartRecovery = $dateStartRecovery;

        return $this;
    }

    public function getDateNoticeDdtm(): ?DateTime
    {
        return $this->dateNoticeDdtm;
    }

    public function setDateNoticeDdtm(?DateTime $dateNoticeDdtm): self
    {
        $this->dateNoticeDdtm = $dateNoticeDdtm;

        return $this;
    }

    public function getPenalties(): Collection
    {
        return $this->penalties;
    }

    public function addPenalty(Penalty $penalty): self
    {
        if (!$this->penalties->contains($penalty)) {
            $this->penalties->add($penalty);
        }

        return $this;
    }

    public function removePenalty(Penalty $penalty): void
    {
        $this->penalties->removeElement($penalty);
    }

    public function getLiquidations(): Collection
    {
        return $this->liquidations;
    }

    public function addLiquidation(Liquidation $liquidation): self
    {
        if (!$this->liquidations->contains($liquidation)) {
            $this->liquidations->add($liquidation);
        }

        return $this;
    }

    public function removeLiquidation(Liquidation $liquidation): void
    {
        $this->liquidations->removeElement($liquidation);
    }

    public function getTotalPenaltyRecovery(): ?int
    {
        return $this->totalPenaltyRecovery;
    }

    public function setTotalPenaltyRecovery(?int $totalPenaltyRecovery): self
    {
        $this->totalPenaltyRecovery = $totalPenaltyRecovery;

        return $this;
    }

    public function getAppealPenalties(): Collection
    {
        return $this->appealPenalties;
    }

    public function addAppealPenalty(PenaltyAppeal $appealPenalty): self
    {
        if (!$this->appealPenalties->contains($appealPenalty)) {
            $this->appealPenalties->add($appealPenalty);
        }

        return $this;
    }

    public function removeAppealPenalty(PenaltyAppeal $appealPenalty): void
    {
        $this->appealPenalties->removeElement($appealPenalty);
    }

    public function getContradictories(): Collection
    {
        return $this->contradictories;
    }

    public function addContradictory(Contradictory $contradictory): self
    {
        if (!$this->contradictories->contains($contradictory)) {
            $this->contradictories->add($contradictory);
        }

        return $this;
    }

    public function removeContradictory(Contradictory $contradictory): void
    {
        $this->contradictories->removeElement($contradictory);
    }

    public function getExpulsion(): ?Expulsion
    {
        return $this->expulsion;
    }

    public function setExpulsion(?Expulsion $expulsion): self
    {
        $this->expulsion = $expulsion;

        return $this;
    }

    public function getDemolition(): ?Demolition
    {
        return $this->demolition;
    }

    public function setDemolition(?Demolition $demolition): self
    {
        $this->demolition = $demolition;

        return $this;
    }
}
