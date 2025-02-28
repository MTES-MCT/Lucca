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
 * Human
 *
 * @ORM\Table(name="lucca_minute_human")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\HumanRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Human implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** STATUS constants */
    const STATUS_OWNER = 'choice.status.owner';
    const STATUS_UNDIVIDED = 'choice.status.undivided';
    const STATUS_USUFRUCT = 'choice.status.usufruct';
    const STATUS_OCCUPANT = 'choice.status.occupant';
    const STATUS_NEIGHBOUR = 'choice.status.neighbour';
    const STATUS_OTHER = 'choice.status.other';
    /** STATUS constants */
    const STATUS_DIRECTOR = 'choice.status.director';
    const STATUS_MANAGER = 'choice.status.manager';
    const STATUS_LEADER = 'choice.status.leader';
    const STATUS_PRESIDENT = 'choice.status.president';
    /** GENDER constants */
    const GENDER_MALE = 'choice.gender.male';
    const GENDER_FEMALE = 'choice.gender.female';
    /** PERSON constants */
    const PERSON_PHYSICAL = 'choice.person.physical';
    const PERSON_CORPORATION = 'choice.person.corporation';

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
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=50)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=30)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 30,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=30)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 30,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="person", type="string", length=30)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 30,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $person;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

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
     * @var string
     *
     * @ORM\Column(name="addressCompany", type="text", nullable=true)
     */
    private $addressCompany;

    /**
     * @var string
     *
     * @ORM\Column(name="statusCompany", type="string", length=30)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 30,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $statusCompany;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Get full name with official syntax
     *
     * @return string
     */
    public function getOfficialName()
    {
        return $this->getName() . ' ' . $this->getFirstname();
    }

    /**
     * Get label display on form
     *
     * @return string
     */
    public function getFormLabel()
    {
        return '(' . $this->getId() . ') ' . $this->getOfficialName();
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Humain';
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
     * Set name
     *
     * @param string $name
     *
     * @return Human
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Human
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Human
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Human
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set person
     *
     * @param string $person
     *
     * @return Human
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return string
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Human
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set company
     *
     * @param string $company
     *
     * @return Human
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
     * Set addressCompany
     *
     * @param string $addressCompany
     *
     * @return Human
     */
    public function setAddressCompany($addressCompany)
    {
        $this->addressCompany = $addressCompany;

        return $this;
    }

    /**
     * Get addressCompany
     *
     * @return string
     */
    public function getAddressCompany()
    {
        return $this->addressCompany;
    }

    /**
     * Set statusCompany
     *
     * @param string $statusCompany
     *
     * @return Human
     */
    public function setStatusCompany($statusCompany)
    {
        $this->statusCompany = $statusCompany;

        return $this;
    }

    /**
     * Get statusCompany
     *
     * @return string
     */
    public function getStatusCompany()
    {
        return $this->statusCompany;
    }
}
