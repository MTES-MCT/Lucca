<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\DecisionBundle\Repository\DecisionRepository;
use Lucca\Bundle\LogBundle\Entity\LogInterface;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\ParameterBundle\Entity\Tribunal;
#[ORM\Entity(repositoryClass: DecisionRepository::class)]
#[ORM\Table(name: "lucca_minute_decision")]
class Decision implements LogInterface
{
    use TimestampableTrait;

    /** TYPE constants */
    public const STATUS_REGULARIZED = 'choice.status.regularized';
    public const STATUS_DEMOLITION = 'choice.status.demolition';
    public const STATUS_EXEC_OFFICE = 'choice.status.exec_office';
    public const STATUS_RELAXED = 'choice.status.relaxed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Minute::class, inversedBy: "decisions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Minute $minute = null;

    #[ORM\ManyToOne(targetEntity: Tribunal::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Tribunal $tribunal = null;

    #[ORM\ManyToOne(targetEntity: Commission::class, cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Commission $tribunalCommission = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private bool $appeal = false;

    #[ORM\ManyToOne(targetEntity: Commission::class, cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Commission $appealCommission = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private ?bool $cassationComplaint = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateAskCassation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateAnswerCassation = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private ?bool $statusCassation = null;

    #[ORM\ManyToOne(targetEntity: Commission::class, cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Commission $cassationCommission = null;

    #[ORM\Column(type: Types::STRING, length: 35, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    private ?string $nameNewCassation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateReferralEurope = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $answerEurope = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dataEurope = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $amountPenaltyDaily = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateStartRecovery = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "constraint.datetime")]
    private ?\DateTime $dateNoticeDdtm = null;

    #[ORM\ManyToMany(targetEntity: Penalty::class, cascade: ["persist", "remove"])]
    #[ORM\JoinTable(name: "lucca_minute_decision_linked_penalty")]
    private Collection $penalties;

    #[ORM\ManyToMany(targetEntity: Liquidation::class, cascade: ["persist", "remove"])]
    #[ORM\JoinTable(name: "lucca_minute_decision_linked_liquidation")]
    private Collection $liquidations;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $totalPenaltyRecovery = null;

    #[ORM\ManyToMany(targetEntity: PenaltyAppeal::class, cascade: ["persist", "remove"])]
    #[ORM\JoinTable(name: "lucca_minute_decision_linked_penalty_appeal")]
    private Collection $appealPenalties;

    #[ORM\ManyToMany(targetEntity: Contradictory::class, cascade: ["persist", "remove"])]
    #[ORM\JoinTable(name: "lucca_minute_decision_linked_contradictory")]
    private Collection $contradictories;

    #[ORM\OneToOne(targetEntity: Expulsion::class, mappedBy: "decision", cascade: ["persist", "remove"])]
    private ?Expulsion $expulsion = null;

    #[ORM\OneToOne(targetEntity: Demolition::class, mappedBy: "decision", cascade: ["persist", "remove"])]
    private ?Demolition $demolition = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Decision constructor
     */
    public function __construct()
    {
        $this->penalties = new ArrayCollection();
        $this->liquidations = new ArrayCollection();
        $this->appealPenalties = new ArrayCollection();
        $this->contradictories = new ArrayCollection();
    }
    /**
     * Add penalty
     *
     * @param Penalty $penalty
     *
     * @return Decision
     */
    public function addPenalty(Penalty $penalty): static
    {
        $this->penalties[] = $penalty;

        return $this;
    }


    /**
     * Add liquidation
     *
     * @param Liquidation $liquidation
     *
     * @return Decision
     */
    public function addLiquidation(Liquidation $liquidation): static
    {
        $this->liquidations[] = $liquidation;

        return $this;
    }

    /**
     * Add contradictory
     *
     * @param Contradictory $contradictory
     *
     * @return Decision
     */
    public function addContradictory(Contradictory $contradictory): static
    {
        $this->contradictories[] = $contradictory;

        return $this;
    }

    /**
     * Set expulsion
     *
     * @param Expulsion $expulsion
     * @return Decision
     */
    public function setExpulsion(Expulsion $expulsion = null): static
    {
        $this->expulsion = $expulsion;
        if ($expulsion !== null)
            $expulsion->setDecision($this);

        return $this;
    }

    /**
     * Set demolition
     *
     * @param Demolition $demolition
     * @return Decision
     */
    public function setDemolition(Demolition $demolition = null): static
    {
        $this->demolition = $demolition;
        if ($demolition !== null)
            $demolition->setDecision($this);

        return $this;
    }

    /**
     * Set minute
     *
     * @param Minute $minute
     *
     * @return Decision
     */
    public function setMinute(Minute $minute): static
    {
        $this->minute = $minute;
        $minute->addDecision($this);

        return $this;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Décision';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set appeal
     *
     * @param boolean $appeal
     *
     * @return Decision
     */
    public function setAppeal(bool $appeal): static
    {
        $this->appeal = $appeal;

        return $this;
    }

    /**
     * Get appeal
     *
     * @return boolean
     */
    public function getAppeal(): bool
    {
        return $this->appeal;
    }

    /**
     * Set cassationComplaint
     *
     * @param boolean $cassationComplaint
     *
     * @return Decision
     */
    public function setCassationComplaint(bool $cassationComplaint): static
    {
        $this->cassationComplaint = $cassationComplaint;

        return $this;
    }

    /**
     * Get cassationComplaint
     *
     * @return boolean
     */
    public function getCassationComplaint(): ?bool
    {
        return $this->cassationComplaint;
    }

    /**
     * Set dateAskCassation
     *
     * @param \DateTime $dateAskCassation
     *
     * @return Decision
     */
    public function setDateAskCassation(\DateTime $dateAskCassation): static
    {
        $this->dateAskCassation = $dateAskCassation;

        return $this;
    }

    /**
     * Get dateAskCassation
     *
     * @return \DateTime
     */
    public function getDateAskCassation(): ?\DateTime
    {
        return $this->dateAskCassation;
    }

    /**
     * Set dateAnswerCassation
     *
     * @param \DateTime $dateAnswerCassation
     *
     * @return Decision
     */
    public function setDateAnswerCassation(\DateTime $dateAnswerCassation): static
    {
        $this->dateAnswerCassation = $dateAnswerCassation;

        return $this;
    }

    /**
     * Get dateAnswerCassation
     *
     * @return \DateTime
     */
    public function getDateAnswerCassation(): ?\DateTime
    {
        return $this->dateAnswerCassation;
    }

    /**
     * Set statusCassation
     *
     * @param boolean $statusCassation
     *
     * @return Decision
     */
    public function setStatusCassation(bool $statusCassation): static
    {
        $this->statusCassation = $statusCassation;

        return $this;
    }

    /**
     * Get statusCassation
     *
     * @return boolean
     */
    public function getStatusCassation(): ?bool
    {
        return $this->statusCassation;
    }

    /**
     * Set nameNewCassation
     *
     * @param string $nameNewCassation
     *
     * @return Decision
     */
    public function setNameNewCassation(string $nameNewCassation): static
    {
        $this->nameNewCassation = $nameNewCassation;

        return $this;
    }

    /**
     * Get nameNewCassation
     *
     * @return string
     */
    public function getNameNewCassation(): ?string
    {
        return $this->nameNewCassation;
    }

    /**
     * Set dateReferralEurope
     *
     * @param \DateTime $dateReferralEurope
     *
     * @return Decision
     */
    public function setDateReferralEurope(\DateTime $dateReferralEurope): static
    {
        $this->dateReferralEurope = $dateReferralEurope;

        return $this;
    }

    /**
     * Get dateReferralEurope
     *
     * @return \DateTime
     */
    public function getDateReferralEurope(): ?\DateTime
    {
        return $this->dateReferralEurope;
    }

    /**
     * Set answerEurope
     *
     * @param \DateTime $answerEurope
     *
     * @return Decision
     */
    public function setAnswerEurope(\DateTime $answerEurope): static
    {
        $this->answerEurope = $answerEurope;

        return $this;
    }

    /**
     * Get answerEurope
     *
     * @return \DateTime
     */
    public function getAnswerEurope(): ?\DateTime
    {
        return $this->answerEurope;
    }

    /**
     * Set dataEurope
     *
     * @param string $dataEurope
     *
     * @return Decision
     */
    public function setDataEurope(string $dataEurope): static
    {
        $this->dataEurope = $dataEurope;

        return $this;
    }

    /**
     * Get dataEurope
     *
     * @return string
     */
    public function getDataEurope(): ?string
    {
        return $this->dataEurope;
    }

    /**
     * Set amountPenaltyDaily
     *
     * @param integer $amountPenaltyDaily
     *
     * @return Decision
     */
    public function setAmountPenaltyDaily(int $amountPenaltyDaily): static
    {
        $this->amountPenaltyDaily = $amountPenaltyDaily;

        return $this;
    }

    /**
     * Get amountPenaltyDaily
     *
     * @return integer
     */
    public function getAmountPenaltyDaily(): ?int
    {
        return $this->amountPenaltyDaily;
    }

    /**
     * Set dateStartRecovery
     *
     * @param \DateTime $dateStartRecovery
     *
     * @return Decision
     */
    public function setDateStartRecovery(\DateTime $dateStartRecovery): static
    {
        $this->dateStartRecovery = $dateStartRecovery;

        return $this;
    }

    /**
     * Get dateStartRecovery
     *
     * @return \DateTime
     */
    public function getDateStartRecovery(): ?\DateTime
    {
        return $this->dateStartRecovery;
    }

    /**
     * Set dateNoticeDdtm
     *
     * @param \DateTime $dateNoticeDdtm
     *
     * @return Decision
     */
    public function setDateNoticeDdtm(\DateTime $dateNoticeDdtm): static
    {
        $this->dateNoticeDdtm = $dateNoticeDdtm;

        return $this;
    }

    /**
     * Get dateNoticeDdtm
     *
     * @return \DateTime
     */
    public function getDateNoticeDdtm(): ?\DateTime
    {
        return $this->dateNoticeDdtm;
    }

    /**
     * Set totalPenaltyRecovery
     *
     * @param integer $totalPenaltyRecovery
     *
     * @return Decision
     */
    public function setTotalPenaltyRecovery(int $totalPenaltyRecovery): static
    {
        $this->totalPenaltyRecovery = $totalPenaltyRecovery;

        return $this;
    }

    /**
     * Get totalPenaltyRecovery
     *
     * @return integer
     */
    public function getTotalPenaltyRecovery(): ?int
    {
        return $this->totalPenaltyRecovery;
    }

    /**
     * Get minute
     *
     * @return Minute
     */
    public function getMinute(): ?Minute
    {
        return $this->minute;
    }

    /**
     * Set tribunal
     *
     * @param $tribunal
     *
     * @return Decision
     */
    public function setTribunal(Tribunal $tribunal = null): static
    {
        $this->tribunal = $tribunal;

        return $this;
    }

    /**
     * Get tribunal
     *
     * @return Tribunal
     */
    public function getTribunal(): ?Tribunal
    {
        return $this->tribunal;
    }

    /**
     * Set tribunalCommission
     *
     * @param Commission $tribunalCommission
     *
     * @return Decision
     */
    public function setTribunalCommission(Commission $tribunalCommission = null): static
    {
        $this->tribunalCommission = $tribunalCommission;

        return $this;
    }

    /**
     * Get tribunalCommission
     *
     * @return Commission
     */
    public function getTribunalCommission(): ?Commission
    {
        return $this->tribunalCommission;
    }

    /**
     * Set appealCommission
     *
     * @param Commission $appealCommission
     *
     * @return Decision
     */
    public function setAppealCommission(Commission $appealCommission = null): static
    {
        $this->appealCommission = $appealCommission;

        return $this;
    }

    /**
     * Get appealCommission
     *
     * @return Commission
     */
    public function getAppealCommission(): ?Commission
    {
        return $this->appealCommission;
    }

    /**
     * Set cassationComission
     *
     * @param Commission $cassationComission
     *
     * @return Decision
     */
    public function setCassationComission(Commission $cassationComission = null): static
    {
        $this->cassationComission = $cassationComission;

        return $this;
    }

    /**
     * Get cassationComission
     *
     * @return Commission
     */
    public function getCassationComission(): Commission
    {
        return $this->cassationComission;
    }

    /**
     * Remove penalty
     *
     * @param Penalty $penalty
     */
    public function removePenalty(Penalty $penalty): void
    {
        $this->penalties->removeElement($penalty);
    }

    /**
     * Get penalties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPenalties(): ArrayCollection|Collection
    {
        return $this->penalties;
    }

    /**
     * Remove liquidation
     *
     * @param Liquidation $liquidation
     */
    public function removeLiquidation(Liquidation $liquidation): void
    {
        $this->liquidations->removeElement($liquidation);
    }

    /**
     * Get liquidations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLiquidations(): ArrayCollection|Collection
    {
        return $this->liquidations;
    }

    /**
     * Add appealPenalty
     *
     * @param PenaltyAppeal $appealPenalty
     *
     * @return Decision
     */
    public function addAppealPenalty(PenaltyAppeal $appealPenalty): static
    {
        $this->appealPenalties[] = $appealPenalty;

        return $this;
    }

    /**
     * Remove appealPenalty
     *
     * @param PenaltyAppeal $appealPenalty
     */
    public function removeAppealPenalty(PenaltyAppeal $appealPenalty): void
    {
        $this->appealPenalties->removeElement($appealPenalty);
    }

    /**
     * Get appealPenalties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAppealPenalties(): ArrayCollection|Collection
    {
        return $this->appealPenalties;
    }

    /**
     * Remove contradictory
     *
     * @param Contradictory $contradictory
     */
    public function removeContradictory(Contradictory $contradictory): void
    {
        $this->contradictories->removeElement($contradictory);
    }

    /**
     * Get contradictories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContradictories(): ArrayCollection|Collection
    {
        return $this->contradictories;
    }

    /**
     * Get expulsion
     *
     * @return Expulsion
     */
    public function getExpulsion(): ?Expulsion
    {
        return $this->expulsion;
    }

    /**
     * Get demolition
     *
     * @return Demolition
     */
    public function getDemolition(): ?Demolition
    {
        return $this->demolition;
    }
}
