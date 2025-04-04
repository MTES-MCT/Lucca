<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\ToggleableTrait;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\ParameterBundle\Repository\TribunalRepository;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Entity(repositoryClass: TribunalRepository::class)]
#[ORM\Table(name: 'lucca_parameter_tribunal')]
class Tribunal implements LoggableInterface
{
    use ToggleableTrait, TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 1, max: 20, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $code;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\ManyToOne(targetEntity: Town::class)]
    private ?Town $office = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 80, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $interlocutor = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 80, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $address = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 80, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $addressCpl = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 10, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $zipCode = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $city = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $region = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Country(message: 'constraint.country')]
    private ?string $country = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Get label display on form
     */
    public function getFormLabel(): string
    {
        return '( ' . $this->getCode() . ' ) ' . $this->getName();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Tribunal';
    }

    /**
     * Get full address formated from HTML
     */
    public function getFullAddress(): string
    {
        $address = '';
        if ($this->getInterlocutor()) {
            $address .= $this->getInterlocutor() . '<br>';
        }

        if ($this->getAddress()) {
            $address .= $this->getAddress() . '<br>';
        }

        if ($this->getAddressCpl()) {
            $address .= $this->getAddressCpl() . '<br>';
        }

        if ($this->getZipCode()) {
            $address .= $this->getZipCode() . ', ';
        }

        if ($this->getCity()) {
            $address .= $this->getCity() . '<br>';
        }

        if ($this->getRegion()) {
            $address .= $this->getRegion() . '<br>';
        }

        if ($this->getCountry()) {
            $address .= Countries::getName($this->getCountry()) . '<br>';
        }

        return '<address>' . $address . '</address>';
    }

    /**
     * Get full address formated from HTML
     */
    public function getInlineAddress(): string
    {
        $address = '';
        if ($this->getInterlocutor()) {
            $address .= $this->getInterlocutor() . ', ';
        }

        if ($this->getAddress()) {
            $address .= $this->getAddress() . ', ';
        }

        if ($this->getAddressCpl()) {
            $address .= $this->getAddressCpl() . ', ';
        }

        if ($this->getZipCode()) {
            $address .= $this->getZipCode() . ', ';
        }

        if ($this->getCity()) {
            $address .= $this->getCity() . ' ';
        }

        if ($this->getRegion()) {
            $address .= $this->getRegion() . ', ';
        }

        if ($this->getCountry()) {
            $address .= Countries::getName($this->getCountry()) . '<br>';
        }

        return '<address>' . $address . '</address>';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getOffice(): ?Town
    {
        return $this->office;
    }

    public function setOffice(?Town $office): self
    {
        $this->office = $office;

        return $this;
    }

    public function getInterlocutor(): ?string
    {
        return $this->interlocutor;
    }

    public function setInterlocutor(?string $interlocutor): self
    {
        $this->interlocutor = $interlocutor;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddressCpl(): ?string
    {
        return $this->addressCpl;
    }

    public function setAddressCpl(?string $addressCpl): self
    {
        $this->addressCpl = $addressCpl;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }
}
