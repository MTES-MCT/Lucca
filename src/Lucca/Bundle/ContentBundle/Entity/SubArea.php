<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Entity;

use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\ContentBundle\Repository\SubAreaRepository;
use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Entity(repositoryClass: SubAreaRepository::class)]
#[ORM\Table(name: 'lucca_content_subarea')]
class SubArea implements LoggableInterface
{
    /** Traits */
    use ToggleableTrait, TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Area::class, inversedBy: 'subareas')]
    #[ORM\JoinColumn(nullable: false)]
    private Area $area;

    #[ORM\OneToMany(targetEntity: Page::class, mappedBy: 'subarea', cascade: ['remove'])]
    private Collection $pages;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\Column(length: 60, unique: true, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 60, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $code = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private int $position = 0;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $width;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 3, max: 20, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $color;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $title;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Sous-zone';
    }

    /**
     * Log name of this Class
     */
    public function __toString(): string
    {
        return $this->getId() . ' - ' . $this->getName();
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArea(): Area
    {
        return $this->area;
    }

    public function setArea(Area $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setSubarea($this);
        }

        return $this;
    }

    public function removePage(Page $page): void
    {
        $this->pages->removeElement($page);
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getWidth(): string
    {
        return $this->width;
    }

    public function setWidth(string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
