<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ChecklistBundle\Entity;

use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\ChecklistBundle\Repository\ChecklistRepository;
use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Entity(repositoryClass: ChecklistRepository::class)]
#[ORM\Table(name: 'lucca_checklist')]
class Checklist implements LoggableInterface
{
    use ToggleableTrait, TimestampableTrait;

    /** TYPE constants */
    const STATUS_MINUTE = 'choice.status.minute';
    const STATUS_UPDATING = 'choice.status.updating';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\OneToMany(targetEntity: Element::class, mappedBy: 'checklist', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $elements;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private Department $department;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Checklist';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
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

    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements[] = $element;
            $element->setChecklist($this);
        }

        return $this;
    }

    public function removeElement(Element $element): void
    {
        $this->elements->removeElement($element);
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
