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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Courier
 *
 * @ORM\Table(name="lucca_minute_courier")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\CourierRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Courier implements LogInterface
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
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Folder", mappedBy="courier", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $folder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOffender", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateOffender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateJudicial", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateJudicial;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="text", nullable=true)
     */
    private $context;

    /**
     * @var bool
     *
     * @ORM\Column(name="civilParty", type="boolean", nullable=true)
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $civilParty = false;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDdtm", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateDdtm;

    /**
     * @ORM\OneToOne(
     *     targetEntity="Lucca\MinuteBundle\Entity\CourierEdition",
     *     cascade={"persist", "remove"}, orphanRemoval=true
     *     )
     * @ORM\JoinColumn(nullable=true)
     */
    private $edition;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Lucca\MinuteBundle\Entity\CourierHumanEdition", mappedBy="courier",
     *     cascade={"persist", "remove"}, orphanRemoval=true
     * )
     */
    private $humansEditions;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Courier constructor
     */
    public function __construct()
    {
        $this->humansEditions = new ArrayCollection();
    }

    /**
     * Add humansEdition
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierHumanEdition $humansEdition
     * @return Courier
     */
    public function addHumansEdition(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierHumanEdition $humansEdition)
    {
        $this->humansEditions[] = $humansEdition;
        $humansEdition->setCourier($this);

        return $this;
    }

    /**
     * Set folder
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder
     * @return Courier
     */
    public function setFolder(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder)
    {
        $this->folder = $folder;
        $folder->setCourier($this);

        return $this;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Courrier';
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
     * Set dateOffender
     *
     * @param \DateTime $dateOffender
     *
     * @return Courier
     */
    public function setDateOffender($dateOffender)
    {
        $this->dateOffender = $dateOffender;

        return $this;
    }

    /**
     * Get dateOffender
     *
     * @return \DateTime
     */
    public function getDateOffender()
    {
        return $this->dateOffender;
    }

    /**
     * Set dateJudicial
     *
     * @param \DateTime $dateJudicial
     *
     * @return Courier
     */
    public function setDateJudicial($dateJudicial)
    {
        $this->dateJudicial = $dateJudicial;

        return $this;
    }

    /**
     * Get dateJudicial
     *
     * @return \DateTime
     */
    public function getDateJudicial()
    {
        return $this->dateJudicial;
    }

    /**
     * Set context
     *
     * @param string $context
     *
     * @return Courier
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set civilParty
     *
     * @param boolean $civilParty
     *
     * @return Courier
     */
    public function setCivilParty($civilParty)
    {
        $this->civilParty = $civilParty;

        return $this;
    }

    /**
     * Get civilParty
     *
     * @return boolean
     */
    public function getCivilParty()
    {
        return $this->civilParty;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Courier
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set dateDdtm
     *
     * @param \DateTime $dateDdtm
     *
     * @return Courier
     */
    public function setDateDdtm($dateDdtm)
    {
        $this->dateDdtm = $dateDdtm;

        return $this;
    }

    /**
     * Get dateDdtm
     *
     * @return \DateTime
     */
    public function getDateDdtm()
    {
        return $this->dateDdtm;
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

    /**
     * Set edition
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierEdition $edition
     *
     * @return Courier
     */
    public function setEdition(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierEdition $edition = null)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierEdition
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * Remove humansEdition
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierHumanEdition $humansEdition
     */
    public function removeHumansEdition(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierHumanEdition $humansEdition)
    {
        $this->humansEditions->removeElement($humansEdition);
    }

    /**
     * Get humansEditions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHumansEditions()
    {
        return $this->humansEditions;
    }
}
