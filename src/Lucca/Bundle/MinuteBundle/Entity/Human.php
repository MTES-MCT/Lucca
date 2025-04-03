<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MinuteBundle\Repository\HumanRepository;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Table(name: 'lucca_minute_human')]
#[ORM\Entity(repositoryClass: HumanRepository::class)]
class Human implements LoggableInterface
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

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $firstname;

    #[ORM\Column(length: 30)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 30, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $status;

    #[ORM\Column(length: 30)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 30, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $gender;

    #[ORM\Column(length: 30)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 30, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $person;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $company = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $addressCompany = null;

    #[ORM\Column(length: 30)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 30, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $statusCompany;

    /************************************************************************ Custom functions ************************************************************************/

    public function getOfficialName(): string
    {
        return $this->getName() . ' ' . $this->getFirstname();
    }

    public function getFormLabel(): string
    {
        return '(' . $this->getId() . ') ' . $this->getOfficialName();
    }

    /**
     * @ihneritdoc
     */
    public function getLogName(): string
    {
        return 'Humain';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setPerson(string $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getPerson(): string
    {
        return $this->person;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setAddressCompany(?string $addressCompany): self
    {
        $this->addressCompany = $addressCompany;

        return $this;
    }

    public function getAddressCompany(): ?string
    {
        return $this->addressCompany;
    }

    public function setStatusCompany(string $statusCompany): self
    {
        $this->statusCompany = $statusCompany;

        return $this;
    }

    public function getStatusCompany(): string
    {
        return $this->statusCompany;
    }
}
