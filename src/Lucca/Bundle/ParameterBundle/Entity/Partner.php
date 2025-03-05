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
use Lucca\Bundle\ParameterBundle\Repository\PartnerRepository;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
#[ORM\Table(name: 'lucca_parameter_partner')]
class Partner implements LoggableInterface
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

    #[ORM\Column(length: 150)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 150, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Department $department;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Get label display on form
     */
    public function getFormLabel(): string
    {
        return '( ' . $this->getCode() . ' ) ' . $this->getName();
    }

    /**
     * @ihneritdoc
     */
    public function getLogName(): string
    {
        return 'Partenaire';
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

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): self
    {
        $this->department = $department;

        return $this;
    }
}
