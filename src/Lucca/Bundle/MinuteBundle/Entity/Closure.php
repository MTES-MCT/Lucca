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

/**
 * Closure
 *
 * @ORM\Table(name="lucca_minute_closure")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\ClosureRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Closure implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** TYPE constants status */
    const STATUS_REGULARIZED = 'choice.status.regularized';
    const STATUS_EXEC_OFFICE = 'choice.status.exec_office';
    const STATUS_RELAXED     = 'choice.status.relaxed';
    const STATUS_OTHER       = 'choice.status.other';

    /** TYPE constants nature */
    const NATURE_REGULARIZED_ADMINISTRATIVE = 'choice.natureRegularized.administrative';
    const NATURE_REGULARIZED_FIELD          = 'choice.natureRegularized.field';

    /** TYPE constants initiatingStructure */
    const INITSTRUCT_DDTM  = 'choice.initstruct.ddtm';
    const INITSTRUCT_DDT  = 'choice.initstruct.ddt';
    const INITSTRUCT_TOWN  = 'choice.initstruct.town';
    const INITSTRUCT_OTHER = 'choice.initstruct.other';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Minute", mappedBy="closure")
     * @ORM\JoinColumn(nullable=false)
     */
    private $minute;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=35)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateClosing", type="datetime")
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateClosing;

    /**
     * @var string
     *
     * @ORM\Column(name="natureRegularized", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Choice(choices= {
     *     Closure::NATURE_REGULARIZED_ADMINISTRATIVE,
     *     Closure::NATURE_REGULARIZED_FIELD,
     *     }, message="constraint.closure.natureRegularized"
     * )
     */
    private $natureRegularized;

    /**
     * @var string
     *
     * @ORM\Column(name="initiatingStructure", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Choice(choices= {
     *     Closure::INITSTRUCT_DDTM,
     *     Closure::INITSTRUCT_DDT,
     *     Closure::INITSTRUCT_TOWN,
     *     Closure::INITSTRUCT_OTHER,
     *     }, message="constraint.closure.initiatingStructure"
     * )
     */
    private $initiatingStructure;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=255, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 255,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(name="observation", type="text", nullable=true)
     */
    private $observation;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Set minute
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute
     *
     * @return Closure
     */
    public function setMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute)
    {
        $this->minute = $minute;
        $minute->setClosure($this);

        return $this;
    }

    /**
     * Set minute open
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute
     *
     * @return Closure
     */
    public function setMinuteOpen(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute)
    {
        $this->minute = $minute;
        $minute->setClosure(null);
        $minute->setIsClosed(false);

        return $this;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Cloture';
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
     * Set status.
     *
     * @param string $status
     *
     * @return Closure
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateClosing.
     *
     * @param \DateTime $dateClosing
     *
     * @return Closure
     */
    public function setDateClosing($dateClosing)
    {
        $this->dateClosing = $dateClosing;

        return $this;
    }

    /**
     * Get dateClosing.
     *
     * @return \DateTime
     */
    public function getDateClosing()
    {
        return $this->dateClosing;
    }

    /**
     * Set natureRegularized.
     *
     * @param string|null $natureRegularized
     *
     * @return Closure
     */
    public function setNatureRegularized($natureRegularized = null)
    {
        $this->natureRegularized = $natureRegularized;

        return $this;
    }

    /**
     * Get natureRegularized.
     *
     * @return string|null
     */
    public function getNatureRegularized()
    {
        return $this->natureRegularized;
    }

    /**
     * Set initiatingStructure.
     *
     * @param string|null $initiatingStructure
     *
     * @return Closure
     */
    public function setInitiatingStructure($initiatingStructure = null)
    {
        $this->initiatingStructure = $initiatingStructure;

        return $this;
    }

    /**
     * Get initiatingStructure.
     *
     * @return string|null
     */
    public function getInitiatingStructure()
    {
        return $this->initiatingStructure;
    }

    /**
     * Set reason.
     *
     * @param string|null $reason
     *
     * @return Closure
     */
    public function setReason($reason = null)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason.
     *
     * @return string|null
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set observation.
     *
     * @param string|null $observation
     *
     * @return Closure
     */
    public function setObservation($observation = null)
    {
        $this->observation = $observation;

        return $this;
    }

    /**
     * Get observation.
     *
     * @return string|null
     */
    public function getObservation()
    {
        return $this->observation;
    }

    /**
     * Get minute.
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute
     */
    public function getMinute()
    {
        return $this->minute;
    }
}
