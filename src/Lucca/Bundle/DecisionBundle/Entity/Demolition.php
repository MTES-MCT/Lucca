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
 * Demolition
 *
 * @ORM\Table(name="lucca_minute_demolition")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\DemolitionRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Demolition implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** RESULT constants */
    const RESULT_REALIZED = 'choice.result.realized';
    const RESULT_REPORTED = 'choice.result.reported';
    const RESULT_CANCELLED = 'choice.result.cancelled';
    const RESULT_WAITING = 'choice.result.waiting';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Lucca\MinuteBundle\Entity\Decision", inversedBy="demolition")
     * @ORM\JoinColumn(nullable=false)
     */
    private $decision;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $company;

    /**
     * @var int
     *
     * @ORM\Column(name="amountCompany", type="integer", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $amountCompany;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDemolition", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateDemolition;

    /**
     * @var string
     *
     * @ORM\Column(name="bailif", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $bailif;


    /**
     * @var int
     *
     * @ORM\Column(name="amountBailif", type="integer", nullable=true)
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $amountBailif;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Profession", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="lucca_minute_demolition_linked_profession",
     *      joinColumns={@ORM\JoinColumn(name="demolition_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="profession_id", referencedColumnName="id")}
     * )
     */
    private $professions;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="result", type="string", length=30, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 30,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $result;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Demolition constructor
     */
    public function __construct()
    {
        $this->professions = new ArrayCollection();
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Démolition';
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
     * Set company
     *
     * @param string $company
     *
     * @return Demolition
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set amountCompany
     *
     * @param integer $amountCompany
     *
     * @return Demolition
     */
    public function setAmountCompany($amountCompany)
    {
        $this->amountCompany = $amountCompany;

        return $this;
    }

    /**
     * Get amountCompany
     *
     * @return integer
     */
    public function getAmountCompany()
    {
        return $this->amountCompany;
    }

    /**
     * Set dateDemolition
     *
     * @param \DateTime $dateDemolition
     *
     * @return Demolition
     */
    public function setDateDemolition($dateDemolition)
    {
        $this->dateDemolition = $dateDemolition;

        return $this;
    }

    /**
     * Get dateDemolition
     *
     * @return \DateTime
     */
    public function getDateDemolition()
    {
        return $this->dateDemolition;
    }

    /**
     * Set bailif
     *
     * @param string $bailif
     *
     * @return Demolition
     */
    public function setBailif($bailif)
    {
        $this->bailif = $bailif;

        return $this;
    }

    /**
     * Get bailif
     *
     * @return string
     */
    public function getBailif()
    {
        return $this->bailif;
    }

    /**
     * Set amountBailif
     *
     * @param integer $amountBailif
     *
     * @return Demolition
     */
    public function setAmountBailif($amountBailif)
    {
        $this->amountBailif = $amountBailif;

        return $this;
    }

    /**
     * Get amountBailif
     *
     * @return integer
     */
    public function getAmountBailif()
    {
        return $this->amountBailif;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Demolition
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set result
     *
     * @param string $result
     *
     * @return Demolition
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set decision
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision $decision
     *
     * @return Demolition
     */
    public function setDecision(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision $decision)
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * Get decision
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * Add profession
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Profession $profession
     *
     * @return Demolition
     */
    public function addProfession(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Profession $profession)
    {
        $this->professions[] = $profession;

        return $this;
    }

    /**
     * Remove profession
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Profession $profession
     */
    public function removeProfession(\Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Profession $profession)
    {
        $this->professions->removeElement($profession);
    }

    /**
     * Get professions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfessions()
    {
        return $this->professions;
    }
}
