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
 * MinuteStory
 *
 * @ORM\Table(name="lucca_minute_minute_story")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\MinuteStoryRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class MinuteStory implements LogInterface
{
    use TimestampableTrait;

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
     * Many Minute Story can be associated to one minute
     *
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Minute", inversedBy="historic")
     * @ORM\JoinColumn(nullable=false)
     */
    private $minute;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdate", type="datetime", nullable=false)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateUpdate;

    /**
     * @var string
     *
     * ** this attr is on nullable=true  because it's an automatic attr
     * @ORM\Column(name="status", type="string", length=50, nullable=false)
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
    private $updatingBy;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * MinuteStory constructor
     */
    public function __construct()
    {
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Historique du dossier';
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
     * Set dateUpdate.
     *
     * @param \DateTime $dateUpdate
     *
     * @return MinuteStory
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate.
     *
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return MinuteStory
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
     * Set minute.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute
     *
     * @return MinuteStory
     */
    public function setMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute)
    {
        $this->minute = $minute;

        return $this;
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

    /**
     * Set updatingBy.
     *
     * @param \Lucca\AdherentBundle\Entity\Adherent $updatingBy
     *
     * @return MinuteStory
     */
    public function setUpdatingBy(\Lucca\AdherentBundle\Entity\Adherent $updatingBy)
    {
        $this->updatingBy = $updatingBy;

        return $this;
    }

    /**
     * Get updatingBy.
     *
     * @return \Lucca\AdherentBundle\Entity\Adherent
     */
    public function getUpdatingBy()
    {
        return $this->updatingBy;
    }
}
