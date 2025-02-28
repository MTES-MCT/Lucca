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

namespace Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CourierHumanEdition
 *
 * @ORM\Table(name="lucca_minute_courier_human_edition")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\CourierHumanEditionRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierHumanEdition implements LogInterface
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
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Courier", inversedBy="humansEditions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $courier;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Human", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $human;

    /**
     * @var bool
     *
     * @ORM\Column(name="letterOffenderEdited", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $letterOffenderEdited = false;

    /**
     * @var string
     *
     * @ORM\Column(name="letterOffender", type="text", nullable=true)
     */
    private $letterOffender;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Courrier édition par humain';
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
     * Set letterOffenderEdited
     *
     * @param boolean $letterOffenderEdited
     *
     * @return CourierHumanEdition
     */
    public function setLetterOffenderEdited($letterOffenderEdited)
    {
        $this->letterOffenderEdited = $letterOffenderEdited;

        return $this;
    }

    /**
     * Get letterOffenderEdited
     *
     * @return boolean
     */
    public function getLetterOffenderEdited()
    {
        return $this->letterOffenderEdited;
    }

    /**
     * Set letterOffender
     *
     * @param string $letterOffender
     *
     * @return CourierHumanEdition
     */
    public function setLetterOffender($letterOffender)
    {
        $this->letterOffender = $letterOffender;

        return $this;
    }

    /**
     * Get letterOffender
     *
     * @return string
     */
    public function getLetterOffender()
    {
        return $this->letterOffender;
    }

    /**
     * Set courier
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier $courier
     *
     * @return CourierHumanEdition
     */
    public function setCourier(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier $courier)
    {
        $this->courier = $courier;

        return $this;
    }

    /**
     * Get courier
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier
     */
    public function getCourier()
    {
        return $this->courier;
    }

    /**
     * Set human
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human $human
     *
     * @return CourierHumanEdition
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
