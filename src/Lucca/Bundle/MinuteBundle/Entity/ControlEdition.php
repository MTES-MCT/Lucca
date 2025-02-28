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
 * ControlEdition
 *
 * @ORM\Table(name="lucca_minute_control_edition")
 * @ORM\Entity()
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class ControlEdition implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Control", inversedBy="editions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $control;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Human")
     * @ORM\JoinColumn(nullable=false)
     */
    private $human;

    /**
     * @var bool
     *
     * @ORM\Column(name="convocationEdited", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $convocationEdited = false;

    /**
     * @var string
     *
     * @ORM\Column(name="letterConvocation", type="text", nullable=true)
     */
    private $letterConvocation;

    /**
     * @var bool
     *
     * @ORM\Column(name="accessEdited", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $accessEdited = false;

    /**
     * @var string
     *
     * @ORM\Column(name="letterAccess", type="text", nullable=true)
     */
    private $letterAccess;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Contrôle édition';
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
     * Set convocationEdited
     *
     * @param boolean $convocationEdited
     *
     * @return ControlEdition
     */
    public function setConvocationEdited($convocationEdited)
    {
        $this->convocationEdited = $convocationEdited;

        return $this;
    }

    /**
     * Get convocationEdited
     *
     * @return boolean
     */
    public function getConvocationEdited()
    {
        return $this->convocationEdited;
    }

    /**
     * Set letterConvocation
     *
     * @param string $letterConvocation
     *
     * @return ControlEdition
     */
    public function setLetterConvocation($letterConvocation)
    {
        $this->letterConvocation = $letterConvocation;

        return $this;
    }

    /**
     * Get letterConvocation
     *
     * @return string
     */
    public function getLetterConvocation()
    {
        return $this->letterConvocation;
    }

    /**
     * Set accessEdited
     *
     * @param boolean $accessEdited
     *
     * @return ControlEdition
     */
    public function setAccessEdited($accessEdited)
    {
        $this->accessEdited = $accessEdited;

        return $this;
    }

    /**
     * Get accessEdited
     *
     * @return boolean
     */
    public function getAccessEdited()
    {
        return $this->accessEdited;
    }

    /**
     * Set letterAccess
     *
     * @param string $letterAccess
     *
     * @return ControlEdition
     */
    public function setLetterAccess($letterAccess)
    {
        $this->letterAccess = $letterAccess;

        return $this;
    }

    /**
     * Get letterAccess
     *
     * @return string
     */
    public function getLetterAccess()
    {
        return $this->letterAccess;
    }

    /**
     * Set control
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control
     *
     * @return ControlEdition
     */
    public function setControl(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control $control)
    {
        $this->control = $control;

        return $this;
    }

    /**
     * Get control
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * Set human
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $human
     *
     * @return ControlEdition
     */
    public function setHuman(\Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $human)
    {
        $this->human = $human;

        return $this;
    }

    /**
     * Get human
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human
     */
    public function getHuman()
    {
        return $this->human;
    }
}
