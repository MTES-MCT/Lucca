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

namespace Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Minute
 *
 * @ORM\Table(name="lucca_minute")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\MinuteRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Minute implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** ORIGIN constants */
    const ORIGIN_COURIER = 'choice.origin.courier';
    const ORIGIN_PHONE = 'choice.origin.phone';
    const ORIGIN_EAGLE = 'choice.origin.eagle';
    const ORIGIN_AGENT = 'choice.origin.agent';
    const ORIGIN_OTHER = 'choice.origin.other';

    /** STATUS constants */
    const STATUS_OPEN       = 'choice.statusMinute.open';
    const STATUS_CONTROL    = 'choice.statusMinute.control';
    const STATUS_FOLDER     = 'choice.statusMinute.folder';
    const STATUS_COURIER    = 'choice.statusMinute.courier';
    const STATUS_AIT        = 'choice.statusMinute.ait';
    const STATUS_UPDATING   = 'choice.statusMinute.updating';
    const STATUS_DECISION   = 'choice.statusMinute.decision';
    const STATUS_CLOSURE    = 'choice.statusMinute.closure';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="num", type="string", length=20)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $num;

    /**
     * @var string
     *
     * ** this attr is on nullable=true  because it's an automatic attr
     * @ORM\Column(name="status", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Choice(choices= {
     *     Minute::STATUS_OPEN,
     *     Minute::STATUS_CONTROL,
     *     Minute::STATUS_FOLDER,
     *     Minute::STATUS_COURIER,
     *     Minute::STATUS_AIT,
     *     Minute::STATUS_UPDATING,
     *     Minute::STATUS_DECISION,
     *     Minute::STATUS_CLOSURE,
     *     }, message="constraint.closure.initiatingStructure"
     * )
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\AdherentBundle\Entity\Adherent")
     * @ORM\JoinColumn(nullable=false)
     */
    private $adherent;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Plot", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $plot;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\ParameterBundle\Entity\Tribunal")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tribunal;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\ParameterBundle\Entity\Tribunal")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tribunalCompetent;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\AdherentBundle\Entity\Agent")
     * @ORM\JoinColumn(nullable=true)
     */
    private $agent;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Human", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="lucca_minute_linked_human",
     *      joinColumns={@ORM\JoinColumn(name="minute_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="human_id", referencedColumnName="id")}
     * )
     */
    private $humans;

    /**
     * @ORM\OneToMany(targetEntity="Lucca\MinuteBundle\Entity\Control", mappedBy="minute", orphanRemoval=true)
     */
    private $controls;

    /**
     * @ORM\OneToMany(targetEntity="Lucca\MinuteBundle\Entity\Updating", mappedBy="minute", orphanRemoval=true)
     */
    private $updatings;

    /**
     * @ORM\OneToMany(targetEntity="Lucca\MinuteBundle\Entity\Decision", mappedBy="minute", orphanRemoval=true)
     */
    private $decisions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOpening", type="datetime")
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateOpening;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateLastUpdate", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateLastUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateComplaint", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     * @Assert\Range(
     *      min = "2000-01-01", max = "last day of December",
     *      minMessage = "constraint.date.range.min",
     *      maxMessage = "constraint.date.range.max",
     * )
     */
    private $dateComplaint;

    /**
     * @var string
     *
     * @ORM\Column(name="nameComplaint", type="string", length=60, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 60,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $nameComplaint;

    /**
     * @var bool
     *
     * @ORM\Column(name="isClosed", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $isClosed = false;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Closure", inversedBy="minute")
     * @ORM\JoinColumn(nullable=true)
     */
    private $closure;

    /**
     * @var string
     *
     * @ORM\Column(name="reporting", type="text", nullable=true)
     */
    private $reporting;

    /**
     * @var string
     *
     * @ORM\Column(name="origin", type="string", length=30, nullable=true)
     * @Assert\Choice(
     *      choices = {
     *          Minute::ORIGIN_COURIER,
     *          Minute::ORIGIN_PHONE,
     *          Minute::ORIGIN_EAGLE,
     *          Minute::ORIGIN_AGENT,
     *          Minute::ORIGIN_OTHER
     *      }, message = "constraint.choice.origin"
     * )
     */
    private $origin;

    /**
     * @ORM\OneToMany(targetEntity="Lucca\MinuteBundle\Entity\MinuteStory", mappedBy="minute", orphanRemoval=true)
     */
    private $historic;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Minute constructor
     */
    public function __construct()
    {
        $this->dateOpening = new \DateTime('now');
        $this->origin = Minute::ORIGIN_AGENT;
        // Set the status to Open
        $this->status = Minute::STATUS_OPEN;
    }

    /**
     * Get Control with date and hour set for create a new folder
     *
     * @return array
     */
    public function getControlsForFolder()
    {
        $result = array();

        foreach ($this->getControls() as $control) {
            /** Available to Frame 3 */
            if ($control->getType() === Control::TYPE_FOLDER) {
                /** Condition - Control have a date and hour + control is accepted + control is not already used */
                if (($control instanceof Control && $control->getDateControl() && $control->getHourControl())
                    && ($control->getAccepted() !== null && $control->getAccepted() === Control::ACCEPTED_NONE)
                    or $control->getIsFenced() === false && $control->getFolder() === null)
                    $result[] = $control;
            }
        }

        // Sort by createdAt method
        usort($result, function ($a, $b) {
            return $a->getCreatedAt() < $b->getCreatedAt();
        });

        return $result;
    }

    /**
     * Constraint on date
     * Dates cannot be minor than today
     *
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function humanConstraint(ExecutionContextInterface $context)
    {
        if (!$this->getHumans())
            $context->buildViolation('constraint.minute.humans')
                ->atPath('humans')
                ->addViolation();
    }

    /**
     * Constraint on Plot
     * Plot Address or Plot Place must be filled
     *
     * @Assert\Callback()
     * @param ExecutionContextInterface $context
     */
    public function plotConstraint(ExecutionContextInterface $context)
    {
        if (!$this->getPlot()->getAddress() && !$this->getPlot()->getPlace() && !$this->getPlot()->getLatitude() && !$this->getPlot()->getLongitude())
            $context->buildViolation('constraint.plot.address_or_parcel')
                ->atPath('plot.address')
                ->addViolation();
        if (!$this->getPlot()->getAddress() && !$this->getPlot()->getPlace() && !$this->getPlot()->getLatitude() && !$this->getPlot()->getLongitude())
            $context->buildViolation('constraint.plot.address_or_parcel')
                ->atPath('plot.place')
                ->addViolation();

        if (!$this->getPlot()->getAddress() && !$this->getPlot()->getPlace() && !$this->getPlot()->getLatitude() && !$this->getPlot()->getLongitude())
            $context->buildViolation('constraint.plot.locationNeeded')
                ->atPath('plot.longitude')
                ->addViolation();
        if (!$this->getPlot()->getAddress() && !$this->getPlot()->getPlace() && !$this->getPlot()->getLatitude() && !$this->getPlot()->getLongitude())
            $context->buildViolation('constraint.plot.locationNeeded')
                ->atPath('plot.latitude')
                ->addViolation();
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Dossier';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set num.
     *
     * @param string $num
     *
     * @return Minute
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num.
     *
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set status.
     *
     * @param string|null $status
     *
     * @return Minute
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateOpening.
     *
     * @param \DateTime $dateOpening
     *
     * @return Minute
     */
    public function setDateOpening($dateOpening)
    {
        $this->dateOpening = $dateOpening;

        return $this;
    }

    /**
     * Get dateOpening.
     *
     * @return \DateTime
     */
    public function getDateOpening()
    {
        return $this->dateOpening;
    }

    /**
     * Set dateComplaint.
     *
     * @param \DateTime|null $dateComplaint
     *
     * @return Minute
     */
    public function setDateComplaint($dateComplaint = null)
    {
        $this->dateComplaint = $dateComplaint;

        return $this;
    }

    /**
     * Get dateComplaint.
     *
     * @return \DateTime|null
     */
    public function getDateComplaint()
    {
        return $this->dateComplaint;
    }

    /**
     * Set nameComplaint.
     *
     * @param string|null $nameComplaint
     *
     * @return Minute
     */
    public function setNameComplaint($nameComplaint = null)
    {
        $this->nameComplaint = $nameComplaint;

        return $this;
    }

    /**
     * Get nameComplaint.
     *
     * @return string|null
     */
    public function getNameComplaint()
    {
        return $this->nameComplaint;
    }

    /**
     * Set isClosed.
     *
     * @param bool $isClosed
     *
     * @return Minute
     */
    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    /**
     * Get isClosed.
     *
     * @return bool
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     * Set reporting.
     *
     * @param string|null $reporting
     *
     * @return Minute
     */
    public function setReporting($reporting = null)
    {
        $this->reporting = $reporting;

        return $this;
    }

    /**
     * Get reporting.
     *
     * @return string|null
     */
    public function getReporting()
    {
        return $this->reporting;
    }

    /**
     * Set origin.
     *
     * @param string|null $origin
     *
     * @return Minute
     */
    public function setOrigin($origin = null)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin.
     *
     * @return string|null
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set adherent.
     *
     * @param \Lucca\AdherentBundle\Entity\Adherent $adherent
     *
     * @return Minute
     */
    public function setAdherent(\Lucca\AdherentBundle\Entity\Adherent $adherent)
    {
        $this->adherent = $adherent;

        return $this;
    }

    /**
     * Get adherent.
     *
     * @return \Lucca\AdherentBundle\Entity\Adherent
     */
    public function getAdherent()
    {
        return $this->adherent;
    }

    /**
     * Set plot.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Plot $plot
     *
     * @return Minute
     */
    public function setPlot(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Plot $plot)
    {
        $this->plot = $plot;

        return $this;
    }

    /**
     * Get plot.
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Plot
     */
    public function getPlot()
    {
        return $this->plot;
    }

    /**
     * Set tribunal.
     *
     * @param \Lucca\ParameterBundle\Entity\Tribunal|null $tribunal
     *
     * @return Minute
     */
    public function setTribunal(\Lucca\ParameterBundle\Entity\Tribunal $tribunal = null)
    {
        $this->tribunal = $tribunal;

        return $this;
    }

    /**
     * Get tribunal.
     *
     * @return \Lucca\ParameterBundle\Entity\Tribunal|null
     */
    public function getTribunal()
    {
        return $this->tribunal;
    }

    /**
     * Set tribunalCompetent.
     *
     * @param \Lucca\ParameterBundle\Entity\Tribunal|null $tribunalCompetent
     *
     * @return Minute
     */
    public function setTribunalCompetent(\Lucca\ParameterBundle\Entity\Tribunal $tribunalCompetent = null)
    {
        $this->tribunalCompetent = $tribunalCompetent;

        return $this;
    }

    /**
     * Get tribunalCompetent.
     *
     * @return \Lucca\ParameterBundle\Entity\Tribunal|null
     */
    public function getTribunalCompetent()
    {
        return $this->tribunalCompetent;
    }

    /**
     * Set agent.
     *
     * @param \Lucca\AdherentBundle\Entity\Agent|null $agent
     *
     * @return Minute
     */
    public function setAgent(\Lucca\AdherentBundle\Entity\Agent $agent = null)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent.
     *
     * @return \Lucca\AdherentBundle\Entity\Agent|null
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Add human.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $human
     *
     * @return Minute
     */
    public function addHuman(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $human)
    {
        $this->humans[] = $human;

        return $this;
    }

    /**
     * Remove human.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $human
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeHuman(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $human)
    {
        return $this->humans->removeElement($human);
    }

    /**
     * Get humans.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHumans()
    {
        return $this->humans;
    }

    /**
     * Add control.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control
     *
     * @return Minute
     */
    public function addControl(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control)
    {
        $this->controls[] = $control;

        return $this;
    }

    /**
     * Remove control.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeControl(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control)
    {
        return $this->controls->removeElement($control);
    }

    /**
     * Get controls.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getControls()
    {
        return $this->controls;
    }

    /**
     * Add updating.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Updating $updating
     *
     * @return Minute
     */
    public function addUpdating(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Updating $updating)
    {
        $this->updatings[] = $updating;

        return $this;
    }

    /**
     * Remove updating.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Updating $updating
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUpdating(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Updating $updating)
    {
        return $this->updatings->removeElement($updating);
    }

    /**
     * Get updatings.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUpdatings()
    {
        return $this->updatings;
    }

    /**
     * Add decision.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision $decision
     *
     * @return Minute
     */
    public function addDecision(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision $decision)
    {
        $this->decisions[] = $decision;

        return $this;
    }

    /**
     * Remove decision.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision $decision
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDecision(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision $decision)
    {
        return $this->decisions->removeElement($decision);
    }

    /**
     * Get decisions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDecisions()
    {
        return $this->decisions;
    }

    /**
     * Set closure.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Closure|null $closure
     *
     * @return Minute
     */
    public function setClosure(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Closure $closure = null)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * Get closure.
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Closure|null
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * Add historic.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\MinuteStory $historic
     *
     * @return Minute
     */
    public function addHistoric(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\MinuteStory $historic)
    {
        $this->historic[] = $historic;

        return $this;
    }

    /**
     * Remove historic.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\MinuteStory $historic
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeHistoric(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\MinuteStory $historic)
    {
        return $this->historic->removeElement($historic);
    }

    /**
     * Get historic.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistoric()
    {
        return $this->historic;
    }

    /**
     * Set dateLastUpdate.
     *
     * @param \DateTime|null $dateLastUpdate
     *
     * @return Minute
     */
    public function setDateLastUpdate($dateLastUpdate = null)
    {
        $this->dateLastUpdate = $dateLastUpdate;

        return $this;
    }

    /**
     * Get dateLastUpdate.
     *
     * @return \DateTime|null
     */
    public function getDateLastUpdate()
    {
        return $this->dateLastUpdate;
    }
}
