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

/**
 * Updating
 *
 * @ORM\Table(name="lucca_minute_updating")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\UpdatingRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Updating implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** NATURE constants */
    const NATURE_AGGRAVATED = 'choice.nature.aggravated';
    const NATURE_UNCHANGED = 'choice.nature.unchanged';
    const NATURE_REGULARIZED = 'choice.nature.regularized';

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
     * @ORM\Column(name="num", type="string", length=25)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $num;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Minute", inversedBy="updatings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $minute;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Control", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="lucca_minute_updating_linked_control",
     *      joinColumns={@ORM\JoinColumn(name="updating_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="control_id", referencedColumnName="id")}
     * )
     */
    private $controls;

    /**
     * @var string
     *
     * @ORM\Column(name="nature", type="string", length=30, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     */
    private $nature;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->controls = new ArrayCollection();
    }

    /**
     * Set minute
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute
     * @return Updating
     */
    public function setMinute(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute $minute)
    {
        $this->minute = $minute;
        $minute->addUpdating($this);

        return $this;
    }

    /**
     * Get Contol with date and hour set for create a new folder
     *
     * @return array
     */
    public function getControlsForFolder()
    {
        $result = array();

        foreach ($this->controls as $control) {
            /** Condition - Control have a date and hour + control is accepted + control is not already used */
            if (($control instanceof Control && $control->getDateControl() && $control->getHourControl())
                && ($control->getAccepted() !== null && $control->getAccepted() === Control::ACCEPTED_NONE)
                or $control->getIsFenced() === false)
                $result[] = $control;
        }

        return $result;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Actualisation';
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
     * Set num
     *
     * @param string $num
     *
     * @return Updating
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set nature
     *
     * @param string $nature
     *
     * @return Updating
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature
     *
     * @return string
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Updating
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Add control
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control
     *
     * @return Updating
     */
    public function addControl(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control)
    {
        $this->controls[] = $control;

        return $this;
    }

    /**
     * Remove control
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control
     */
    public function removeControl(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control)
    {
        $this->controls->removeElement($control);
    }

    /**
     * Get controls
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getControls()
    {
        return $this->controls;
    }
}
