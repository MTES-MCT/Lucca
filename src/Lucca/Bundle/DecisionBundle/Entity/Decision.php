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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Decision
 *
 * @ORM\Table(name="lucca_minute_decision")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\DecisionRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Decision implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** TYPE constants */
    const STATUS_REGULARIZED = 'choice.status.regularized';
    const STATUS_DEMOLITION = 'choice.status.demolition';
    const STATUS_EXEC_OFFICE = 'choice.status.exec_office';
    const STATUS_RELAXED = 'choice.status.relaxed';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Minute", inversedBy="decisions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $minute;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\ParameterBundle\Entity\Tribunal")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tribunal;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Commission", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tribunalCommission;

    /**
     * @var bool
     *
     * @ORM\Column(name="appeal", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $appeal = false;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Commission", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $appealCommission;

    /**
     * @var bool
     *
     * @ORM\Column(name="cassationComplaint", type="boolean", nullable=true)
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $cassationComplaint = null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAskCassation", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateAskCassation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAnswerCassation", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateAnswerCassation;

    /**
     * @var bool
     *
     * @ORM\Column(name="statusCassation", type="boolean", nullable=true)
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $statusCassation = null;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Commission", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $cassationComission;

    /**
     * @var string
     *
     * @ORM\Column(name="nameNewCassation", type="string", length=35, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $nameNewCassation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateReferralEurope", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateReferralEurope;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="answerEurope", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $answerEurope;

    /**
     * @var string
     *
     * @ORM\Column(name="dataEurope", type="text", nullable=true)
     */
    private $dataEurope;

    /**
     * @var int
     *
     * @ORM\Column(name="amountPenaltyDaily", type="integer", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $amountPenaltyDaily;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStartRecovery", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateStartRecovery;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNoticeDdtm", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateNoticeDdtm;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Penalty", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="lucca_minute_decision_linked_penalty",
     *      joinColumns={@ORM\JoinColumn(name="decision_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="penalty_id", referencedColumnName="id")}
     * )
     */
    private $penalties;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Liquidation", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="lucca_minute_decision_linked_liquidation",
     *      joinColumns={@ORM\JoinColumn(name="decision_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="liquidation_id", referencedColumnName="id")}
     * )
     */
    private $liquidations;

    /**
     * @var int
     *
     * @ORM\Column(name="totalPenaltyRecovery", type="integer", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $totalPenaltyRecovery;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\PenaltyAppeal", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="lucca_minute_decision_linked_penalty_appeal",
     *      joinColumns={@ORM\JoinColumn(name="decision_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="penalty_appeal_id", referencedColumnName="id")}
     * )
     */
    private $appealPenalties;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Contradictory", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="lucca_minute_decision_linked_contradictory",
     *      joinColumns={@ORM\JoinColumn(name="decision_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contradictory_id", referencedColumnName="id")}
     * )
     */
    private $contradictories;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Expulsion", mappedBy="decision", cascade={"persist", "remove"})
     */
    private $expulsion;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Demolition", mappedBy="decision", cascade={"persist", "remove"})
     */
    private $demolition;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Decision constructor
     */
    public function __construct()
    {
        $this->penalties = new ArrayCollection();
        $this->appealPenalties = new ArrayCollection();
        $this->contradictories = new ArrayCollection();
        $this->liquidations = new ArrayCollection();
    }

    /**
     * Add penalty
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Penalty $penalty
     *
     * @return Decision
     */
    public function addPenalty(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Penalty $penalty)
    {
        $this->penalties[] = $penalty;

        return $this;
    }


    /**
     * Add liquidation
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Liquidation $liquidation
     *
     * @return Decision
     */
    public function addLiquidation(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Liquidation $liquidation)
    {
        $this->liquidations[] = $liquidation;

        return $this;
    }

    /**
     * Add contradictory
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Contradictory $contradictory
     *
     * @return Decision
     */
    public function addContradictory(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Contradictory $contradictory)
    {
        $this->contradictories[] = $contradictory;

        return $this;
    }

    /**
     * Set expulsion
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Expulsion $expulsion
     * @return Decision
     */
    public function setExpulsion(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Expulsion $expulsion = null)
    {
        $this->expulsion = $expulsion;
        if ($expulsion !== null)
            $expulsion->setDecision($this);

        return $this;
    }

    /**
     * Set demolition
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Demolition $demolition
     * @return Decision
     */
    public function setDemolition(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Demolition $demolition = null)
    {
        $this->demolition = $demolition;
        if ($demolition !== null)
            $demolition->setDecision($this);

        return $this;
    }

    /**
     * Set minute
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute
     *
     * @return Decision
     */
    public function setMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute)
    {
        $this->minute = $minute;
        $minute->addDecision($this);

        return $this;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Décision';
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
     * Set appeal
     *
     * @param boolean $appeal
     *
     * @return Decision
     */
    public function setAppeal($appeal)
    {
        $this->appeal = $appeal;

        return $this;
    }

    /**
     * Get appeal
     *
     * @return boolean
     */
    public function getAppeal()
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
    public function setCassationComplaint($cassationComplaint)
    {
        $this->cassationComplaint = $cassationComplaint;

        return $this;
    }

    /**
     * Get cassationComplaint
     *
     * @return boolean
     */
    public function getCassationComplaint()
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
    public function setDateAskCassation($dateAskCassation)
    {
        $this->dateAskCassation = $dateAskCassation;

        return $this;
    }

    /**
     * Get dateAskCassation
     *
     * @return \DateTime
     */
    public function getDateAskCassation()
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
    public function setDateAnswerCassation($dateAnswerCassation)
    {
        $this->dateAnswerCassation = $dateAnswerCassation;

        return $this;
    }

    /**
     * Get dateAnswerCassation
     *
     * @return \DateTime
     */
    public function getDateAnswerCassation()
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
    public function setStatusCassation($statusCassation)
    {
        $this->statusCassation = $statusCassation;

        return $this;
    }

    /**
     * Get statusCassation
     *
     * @return boolean
     */
    public function getStatusCassation()
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
    public function setNameNewCassation($nameNewCassation)
    {
        $this->nameNewCassation = $nameNewCassation;

        return $this;
    }

    /**
     * Get nameNewCassation
     *
     * @return string
     */
    public function getNameNewCassation()
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
    public function setDateReferralEurope($dateReferralEurope)
    {
        $this->dateReferralEurope = $dateReferralEurope;

        return $this;
    }

    /**
     * Get dateReferralEurope
     *
     * @return \DateTime
     */
    public function getDateReferralEurope()
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
    public function setAnswerEurope($answerEurope)
    {
        $this->answerEurope = $answerEurope;

        return $this;
    }

    /**
     * Get answerEurope
     *
     * @return \DateTime
     */
    public function getAnswerEurope()
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
    public function setDataEurope($dataEurope)
    {
        $this->dataEurope = $dataEurope;

        return $this;
    }

    /**
     * Get dataEurope
     *
     * @return string
     */
    public function getDataEurope()
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
    public function setAmountPenaltyDaily($amountPenaltyDaily)
    {
        $this->amountPenaltyDaily = $amountPenaltyDaily;

        return $this;
    }

    /**
     * Get amountPenaltyDaily
     *
     * @return integer
     */
    public function getAmountPenaltyDaily()
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
    public function setDateStartRecovery($dateStartRecovery)
    {
        $this->dateStartRecovery = $dateStartRecovery;

        return $this;
    }

    /**
     * Get dateStartRecovery
     *
     * @return \DateTime
     */
    public function getDateStartRecovery()
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
    public function setDateNoticeDdtm($dateNoticeDdtm)
    {
        $this->dateNoticeDdtm = $dateNoticeDdtm;

        return $this;
    }

    /**
     * Get dateNoticeDdtm
     *
     * @return \DateTime
     */
    public function getDateNoticeDdtm()
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
    public function setTotalPenaltyRecovery($totalPenaltyRecovery)
    {
        $this->totalPenaltyRecovery = $totalPenaltyRecovery;

        return $this;
    }

    /**
     * Get totalPenaltyRecovery
     *
     * @return integer
     */
    public function getTotalPenaltyRecovery()
    {
        return $this->totalPenaltyRecovery;
    }

    /**
     * Get minute
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Set tribunal
     *
     * @param \Lucca\ParameterBundle\Entity\Tribunal $tribunal
     *
     * @return Decision
     */
    public function setTribunal(\Lucca\ParameterBundle\Entity\Tribunal $tribunal = null)
    {
        $this->tribunal = $tribunal;

        return $this;
    }

    /**
     * Get tribunal
     *
     * @return \Lucca\ParameterBundle\Entity\Tribunal
     */
    public function getTribunal()
    {
        return $this->tribunal;
    }

    /**
     * Set tribunalCommission
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Commission $tribunalCommission
     *
     * @return Decision
     */
    public function setTribunalCommission(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Commission $tribunalCommission = null)
    {
        $this->tribunalCommission = $tribunalCommission;

        return $this;
    }

    /**
     * Get tribunalCommission
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Commission
     */
    public function getTribunalCommission()
    {
        return $this->tribunalCommission;
    }

    /**
     * Set appealCommission
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Commission $appealCommission
     *
     * @return Decision
     */
    public function setAppealCommission(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Commission $appealCommission = null)
    {
        $this->appealCommission = $appealCommission;

        return $this;
    }

    /**
     * Get appealCommission
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Commission
     */
    public function getAppealCommission()
    {
        return $this->appealCommission;
    }

    /**
     * Set cassationComission
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Commission $cassationComission
     *
     * @return Decision
     */
    public function setCassationComission(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Commission $cassationComission = null)
    {
        $this->cassationComission = $cassationComission;

        return $this;
    }

    /**
     * Get cassationComission
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Commission
     */
    public function getCassationComission()
    {
        return $this->cassationComission;
    }

    /**
     * Remove penalty
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Penalty $penalty
     */
    public function removePenalty(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Penalty $penalty)
    {
        $this->penalties->removeElement($penalty);
    }

    /**
     * Get penalties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPenalties()
    {
        return $this->penalties;
    }

    /**
     * Remove liquidation
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Liquidation $liquidation
     */
    public function removeLiquidation(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Liquidation $liquidation)
    {
        $this->liquidations->removeElement($liquidation);
    }

    /**
     * Get liquidations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLiquidations()
    {
        return $this->liquidations;
    }

    /**
     * Add appealPenalty
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\PenaltyAppeal $appealPenalty
     *
     * @return Decision
     */
    public function addAppealPenalty(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\PenaltyAppeal $appealPenalty)
    {
        $this->appealPenalties[] = $appealPenalty;

        return $this;
    }

    /**
     * Remove appealPenalty
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\PenaltyAppeal $appealPenalty
     */
    public function removeAppealPenalty(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\PenaltyAppeal $appealPenalty)
    {
        $this->appealPenalties->removeElement($appealPenalty);
    }

    /**
     * Get appealPenalties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAppealPenalties()
    {
        return $this->appealPenalties;
    }

    /**
     * Remove contradictory
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Contradictory $contradictory
     */
    public function removeContradictory(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Contradictory $contradictory)
    {
        $this->contradictories->removeElement($contradictory);
    }

    /**
     * Get contradictories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContradictories()
    {
        return $this->contradictories;
    }

    /**
     * Get expulsion
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Expulsion
     */
    public function getExpulsion()
    {
        return $this->expulsion;
    }

    /**
     * Get demolition
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Demolition
     */
    public function getDemolition()
    {
        return $this->demolition;
    }
}
