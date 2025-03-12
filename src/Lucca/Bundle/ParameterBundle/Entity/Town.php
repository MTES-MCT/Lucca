<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\{ToggleableTrait, TimestampableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\ParameterBundle\Repository\TownRepository;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Entity(repositoryClass: TownRepository::class)]
#[ORM\Table(name: 'lucca_parameter_town')]
class Town implements LoggableInterface
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
    private string $code = '';

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $office;

    #[ORM\Column(length: 50)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 1, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $zipcode;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private Department $department;

    #[ORM\ManyToOne(targetEntity: Intercommunal::class)]
    private ?Intercommunal $intercommunal = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Get label display on form
     */
    public function getFormLabel(): string
    {
        return '( ' . $this->getZipcode() . ' ) ' . $this->getName();
    }

    /**
     * Get label display on form
     */
    public function getFormLabelInsee(): string
    {
        return '( ' . $this->getCode() . ' ) ' . $this->getName();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Ville';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOffice(): string
    {
        return $this->office;
    }

    public function setOffice(string $office): self
    {
        $this->office = $office;

        return $this;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getIntercommunal(): ?Intercommunal
    {
        return $this->intercommunal;
    }

    public function setIntercommunal(?Intercommunal $intercommunal): self
    {
        $this->intercommunal = $intercommunal;

        return $this;
    }
}
