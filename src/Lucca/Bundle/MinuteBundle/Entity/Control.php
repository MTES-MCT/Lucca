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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Control
 *
 * @ORM\Table(name="lucca_minute_control")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\ControlRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Control implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** TYPE constants */
    const TYPE_FOLDER = 'choice.type.folder';
    const TYPE_REFRESH = 'choice.type.refresh';
    /** STATE constants */
    const STATE_INSIDE = 'choice.state.inside';
    const STATE_INSIDE_WITHOUT_CONVOCATION = 'choice.state.inside_without_convocation';
    const STATE_OUTSIDE = 'choice.state.outside';
    const STATE_NEIGHBOUR = 'choice.state.neighbour';
    /** REASON constants */
    const REASON_ERROR_ADRESS = 'choice.reason.error_adress';
    const REASON_UNKNOW_ADRESS = 'choice.reason.unknown_adress';
    const REASON_REFUSED_LETTER = 'choice.reason.refused_letter';
    const REASON_UNCLAIMED_LETTER = 'choice.reason.unclaimed_letter';
    /** ACCEPTED constants */
    const ACCEPTED_OK = 'choice.accepted.ok';
    const ACCEPTED_NOK = 'choice.accepted.nok';
    const ACCEPTED_NONE = 'choice.accepted.none';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Minute", inversedBy="controls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $minute;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Human")
     * @ORM\JoinTable(name="lucca_minute_control_linked_human_minute",
     *      joinColumns={@ORM\JoinColumn(name="control_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="human_id", referencedColumnName="id")}
     * )
     */
    private $humansByMinute;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Human", cascade={"persist"})
     * @ORM\JoinTable(name="lucca_minute_control_linked_human_control",
     *      joinColumns={@ORM\JoinColumn(name="control_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="human_id", referencedColumnName="id")}
     * )
     */
    private $humansByControl;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\AdherentBundle\Entity\Agent")
     * @ORM\JoinColumn(nullable=false)
     */
    private $agent;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\AgentAttendant", cascade={"persist"})
     * @ORM\JoinTable(name="lucca_minute_control_linked_agent_attendant",
     *      joinColumns={@ORM\JoinColumn(name="control_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="agent_attendant_id", referencedColumnName="id")}
     * )
     */
    private $agentAttendants;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=25)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 25,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePostal", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $datePostal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateSended", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateSended;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNotified", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateNotified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateReturned", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateReturned;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=60, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 60,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $reason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateContact", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateContact;

    /**
     * @var string
     *
     * @ORM\Column(name="accepted", type="string", length=40, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 40,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $accepted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateControl", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateControl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hourControl", type="time", nullable=true)
     * @Assert\Time(message = "constraint.time")
     */
    private $hourControl;

    /**
     * @var string
     *
     * @ORM\Column(name="stateControl", type="string", length=60)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $stateControl;

    /**
     * @var bool
     *
     * @ORM\Column(name="summoned", type="boolean", nullable=true)
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $summoned;

    /**
     * @var string
     *
     * @ORM\Column(name="courierDelivery", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $courierDelivery;

    /**
     * @var bool
     *
     * @ORM\Column(name="isFenced", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $isFenced = false;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Lucca\MinuteBundle\Entity\ControlEdition", mappedBy="control",
     *     cascade={"persist", "remove"}, orphanRemoval=true
     * )
     */
    private $editions;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Folder", inversedBy="control")
     * @ORM\JoinColumn(nullable=true)
     */
    private $folder;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Control constructor
     *
     * @param $type
     */
    public function __construct($type)
    {
        $this->humansByMinute = new ArrayCollection();
        $this->humansByControl = new ArrayCollection();

        $this->agentAttendants = new ArrayCollection();
        $this->editions = new ArrayCollection();

        $this->setType($type);
    }

    /**
     * Get label displayed on form
     *
     * @return string
     */
    public function getFormLabel()
    {
        if ($this->getDateControl() && $this->getHourControl())
            return $this->getDateControl()->format('d/m/Y') . ' ' . $this->getHourControl()->format('H:i');
        else
            return 'Contrôle non défini';
    }

    /**
     * Add edition
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\ControlEdition $edition
     * @return Control
     */
    public function addEdition(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\ControlEdition $edition)
    {
        $this->editions[] = $edition;
        $edition->setControl($this);

        return $this;
    }

    /**
     * Set minute
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute
     * @return Control
     */
    public function setMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute)
    {
        $this->minute = $minute;
        $minute->addControl($this);

        return $this;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Contrôle';
    }

    /************************************************************************ Custom constraints ************************************************************************/

    /**
     * Constraint on date Sended
     * If data is set, check others date
     *
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function dateSendedConstraint(ExecutionContextInterface $context)
    {
        if ($this->getDateSended()) {

            /** Date send must be greater or equal than date Postal */
            if ($this->getDatePostal() && !($this->getDateSended() >= $this->getDatePostal()))
                $context->buildViolation('constraint.control.send_greater_equal_postal')
                    ->atPath('dateSended')
                    ->addViolation();

            /** Date send must be less than date Notified */
            if ($this->getDateNotified() && !($this->getDateSended() < $this->getDateNotified()))
                $context->buildViolation('constraint.control.send_less_notified')
                    ->atPath('dateSended')
                    ->addViolation();

            /** Date send must be less than date Control */
            if ($this->getDateReturned() && !($this->getDateSended() < $this->getDateReturned()))
                $context->buildViolation('constraint.control.send_less_returned')
                    ->atPath('dateSended')
                    ->addViolation();
        }
    }

    /**
     * Constraint on date Notified
     * If data is set, check others date
     *
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function dateNotifiedConstraint(ExecutionContextInterface $context)
    {
        if ($this->getDateNotified()) {

            /** Date notification must be greater than date Postal */
            if ($this->getDatePostal() && !($this->getDateNotified() > $this->getDatePostal()))
                $context->buildViolation('constraint.control.notified_greater_postal')
                    ->atPath('dateNotified')
                    ->addViolation();

            /** Date notification must be greater than date Send */
            if ($this->getDateSended() && !($this->getDateNotified() > $this->getDateSended()))
                $context->buildViolation('constraint.control.notified_greater_sended')
                    ->atPath('dateNotified')
                    ->addViolation();

            /** Date notification must be greater than date Control */
            if ($this->getDateReturned() && !($this->getDateNotified() < $this->getDateReturned()))
                $context->buildViolation('constraint.control.notified_less_returned')
                    ->atPath('dateNotified')
                    ->addViolation();
        }
    }

    /**
     * Constraint on date Control
     * If data is set, check others date
     *
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function dateControlConstraint(ExecutionContextInterface $context)
    {
        if ($this->getDateReturned()) {

            /** Date control must be greater than date Postal */
            if ($this->getDatePostal() && !($this->getDateReturned() > $this->getDatePostal()))
                $context->buildViolation('constraint.control.returned_greater_postal')
                    ->atPath('dateReturned')
                    ->addViolation();

            /** Date control must be greater than date Send */
            if ($this->getDateSended() && !($this->getDateReturned() > $this->getDateSended()))
                $context->buildViolation('constraint.control.returned_greater_sended')
                    ->atPath('dateReturned')
                    ->addViolation();

            /** Date control must be greater than date Notified */
            if ($this->getDateNotified() && !($this->getDateReturned() > $this->getDateNotified()))
                $context->buildViolation('constraint.control.returned_greater_notified')
                    ->atPath('dateControl')
                    ->addViolation();
        }
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
     * Set type
     *
     * @param string $type
     *
     * @return Control
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set datePostal
     *
     * @param \DateTime $datePostal
     *
     * @return Control
     */
    public function setDatePostal($datePostal)
    {
        $this->datePostal = $datePostal;

        return $this;
    }

    /**
     * Get datePostal
     *
     * @return \DateTime
     */
    public function getDatePostal()
    {
        return $this->datePostal;
    }

    /**
     * Set dateSended
     *
     * @param \DateTime $dateSended
     *
     * @return Control
     */
    public function setDateSended($dateSended)
    {
        $this->dateSended = $dateSended;

        return $this;
    }

    /**
     * Get dateSended
     *
     * @return \DateTime
     */
    public function getDateSended()
    {
        return $this->dateSended;
    }

    /**
     * Set dateNotified
     *
     * @param \DateTime $dateNotified
     *
     * @return Control
     */
    public function setDateNotified($dateNotified)
    {
        $this->dateNotified = $dateNotified;

        return $this;
    }

    /**
     * Get dateNotified
     *
     * @return \DateTime
     */
    public function getDateNotified()
    {
        return $this->dateNotified;
    }

    /**
     * Set dateReturned
     *
     * @param \DateTime $dateReturned
     *
     * @return Control
     */
    public function setDateReturned($dateReturned)
    {
        $this->dateReturned = $dateReturned;

        return $this;
    }

    /**
     * Get dateReturned
     *
     * @return \DateTime
     */
    public function getDateReturned()
    {
        return $this->dateReturned;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return Control
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set dateContact
     *
     * @param \DateTime $dateContact
     *
     * @return Control
     */
    public function setDateContact($dateContact)
    {
        $this->dateContact = $dateContact;

        return $this;
    }

    /**
     * Get dateContact
     *
     * @return \DateTime
     */
    public function getDateContact()
    {
        return $this->dateContact;
    }

    /**
     * Set accepted
     *
     * @param string $accepted
     *
     * @return Control
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return string
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Set dateControl
     *
     * @param \DateTime $dateControl
     *
     * @return Control
     */
    public function setDateControl($dateControl)
    {
        $this->dateControl = $dateControl;

        return $this;
    }

    /**
     * Get dateControl
     *
     * @return \DateTime
     */
    public function getDateControl()
    {
        return $this->dateControl;
    }

    /**
     * Set hourControl
     *
     * @param \DateTime $hourControl
     *
     * @return Control
     */
    public function setHourControl($hourControl)
    {
        $this->hourControl = $hourControl;

        return $this;
    }

    /**
     * Get hourControl
     *
     * @return \DateTime
     */
    public function getHourControl()
    {
        return $this->hourControl;
    }

    /**
     * Set stateControl
     *
     * @param string $stateControl
     *
     * @return Control
     */
    public function setStateControl($stateControl)
    {
        $this->stateControl = $stateControl;

        return $this;
    }

    /**
     * Get stateControl
     *
     * @return string
     */
    public function getStateControl()
    {
        return $this->stateControl;
    }

    /**
     * Set summoned
     *
     * @param boolean $summoned
     *
     * @return Control
     */
    public function setSummoned($summoned)
    {
        $this->summoned = $summoned;

        return $this;
    }

    /**
     * Get summoned
     *
     * @return boolean
     */
    public function getSummoned()
    {
        return $this->summoned;
    }

    /**
     * Set courierDelivery
     *
     * @param string $courierDelivery
     *
     * @return Control
     */
    public function setCourierDelivery($courierDelivery)
    {
        $this->courierDelivery = $courierDelivery;

        return $this;
    }

    /**
     * Get courierDelivery
     *
     * @return string
     */
    public function getCourierDelivery()
    {
        return $this->courierDelivery;
    }

    /**
     * Set isFenced
     *
     * @param boolean $isFenced
     *
     * @return Control
     */
    public function setIsFenced($isFenced)
    {
        $this->isFenced = $isFenced;

        return $this;
    }

    /**
     * Get isFenced
     *
     * @return boolean
     */
    public function getIsFenced()
    {
        return $this->isFenced;
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
     * Add humansByMinute
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByMinute
     *
     * @return Control
     */
    public function addHumansByMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByMinute)
    {
        $this->humansByMinute[] = $humansByMinute;

        return $this;
    }

    /**
     * Remove humansByMinute
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByMinute
     */
    public function removeHumansByMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByMinute)
    {
        $this->humansByMinute->removeElement($humansByMinute);
    }

    /**
     * Get humansByMinute
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHumansByMinute()
    {
        return $this->humansByMinute;
    }

    /**
     * Add humansByControl
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByControl
     *
     * @return Control
     */
    public function addHumansByControl(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByControl)
    {
        $this->humansByControl[] = $humansByControl;

        return $this;
    }

    /**
     * Remove humansByControl
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByControl
     */
    public function removeHumansByControl(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $humansByControl)
    {
        $this->humansByControl->removeElement($humansByControl);
    }

    /**
     * Get humansByControl
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHumansByControl()
    {
        return $this->humansByControl;
    }

    /**
     * Set agent
     *
     * @param \Lucca\AdherentBundle\Entity\Agent $agent
     *
     * @return Control
     */
    public function setAgent(\Lucca\AdherentBundle\Entity\Agent $agent)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return \Lucca\AdherentBundle\Entity\Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Add agentAttendant
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\AgentAttendant $agentAttendant
     *
     * @return Control
     */
    public function addAgentAttendant(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\AgentAttendant $agentAttendant)
    {
        $this->agentAttendants[] = $agentAttendant;

        return $this;
    }

    /**
     * Remove agentAttendant
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\AgentAttendant $agentAttendant
     */
    public function removeAgentAttendant(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\AgentAttendant $agentAttendant)
    {
        $this->agentAttendants->removeElement($agentAttendant);
    }

    /**
     * Get agentAttendants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgentAttendants()
    {
        return $this->agentAttendants;
    }

    /**
     * Remove edition
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\ControlEdition $edition
     */
    public function removeEdition(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\ControlEdition $edition)
    {
        $this->editions->removeElement($edition);
    }

    /**
     * Get editions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEditions()
    {
        return $this->editions;
    }

    /**
     * Set folder
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder
     *
     * @return Control
     */
    public function setFolder(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder = null)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
