<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\FolderBundle\Repository\NatinfRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Table(name: "lucca_natinf")]
#[ORM\Entity(repositoryClass: NatinfRepository::class)]
class Natinf implements LoggableInterface
{
    use ToggleableTrait, TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private int $num;

    #[ORM\Column]
    #[Assert\NotNull(message: "constraint.not_null")]
    private string $qualification;

    #[ORM\Column]
    #[Assert\NotNull(message: "constraint.not_null")]
    private string $definedBy;

    #[ORM\Column]
    #[Assert\NotNull(message: "constraint.not_null")]
    private string $repressedBy;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: "lucca_natinf_linked_tag",
        joinColumns: [new ORM\JoinColumn(name: "natinf_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "tag_id", referencedColumnName: "id")]
    )]
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Department $department;

    #[ORM\ManyToOne(targetEntity: Natinf::class)]
    private ?Natinf $parent = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Label use on form
     */
    public function getFormLabel(): string
    {
        return $this->getNum() . ' / ' . $this->getQualification();
    }

    /**
     * Check if tag exist in array
     */
    public function hasTag(Tag $tag): bool
    {
        if ($this->getTags()) {
            foreach ($this->getTags() as $element) {
                if ($element === $tag) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Natinf';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNum(): int
    {
        return $this->num;
    }

    public function setNum(int $num): self
    {
        $this->num = $num;

        return $this;
    }

    public function getQualification(): string
    {
        return $this->qualification;
    }

    public function setQualification(string $qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function getDefinedBy(): string
    {
        return $this->definedBy;
    }

    public function setDefinedBy(string $definedBy): self
    {
        $this->definedBy = $definedBy;

        return $this;
    }

    public function getRepressedBy(): string
    {
        return $this->repressedBy;
    }

    public function setRepressedBy(string $repressedBy): self
    {
        $this->repressedBy = $repressedBy;

        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

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

    public function getParent(): ?Natinf
    {
        return $this->parent;
    }

    public function setParent(?Natinf $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
