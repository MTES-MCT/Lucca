<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MediaBundle\Entity\{Media, MediaAsyncInterface};
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ModelBundle\Repository\MarginRepository;

#[ORM\Entity(repositoryClass: MarginRepository::class)]
#[ORM\Table(name: 'lucca_model_margin')]
class Margin implements LoggableInterface, MediaAsyncInterface
{
    /** Traits */
    use TimestampableTrait;

    /** POSITION constants */
    const POSITION_TOP = 'choice.position.top';
    const POSITION_BOTTOM = 'choice.position.bottom';
    const POSITION_LEFT = 'choice.position.left';
    const POSITION_RIGHT = 'choice.position.right';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice([
        Margin::POSITION_TOP,
        Margin::POSITION_BOTTOM,
        Margin::POSITION_LEFT,
        Margin::POSITION_RIGHT,
    ], message: 'constraint.position.initiatingStructure')]
    private string $position;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private Department $department;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $height = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $width = null;

    #[ORM\OneToMany(targetEntity: Bloc::class, mappedBy: 'margin', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $blocs;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 3, max: 20, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $background = null;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['persist', 'remove'])]
    private ?Media $backgroundImg = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Set media by asynchronous method.
     */
    public function setAsyncMedia(?Media $media = null): self
    {
        $this->setBackgroundImg($media);

        return $this;
    }

    /**
     * Get media by asynchronous method.
     */
    public function getAsyncMedia(): ?Media
    {
        return $this->getBackgroundImg();
    }

    /**
     * If Margin attribute null is false
     */
    public function isDefined(): bool
    {
        return ($this->height !== null && $this->width !== null);
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);

            // cloning the relation blocs which is a OneToMany
            $blocsClone = new ArrayCollection();
            /** @var Bloc $item */
            foreach ($this->blocs as $item) {
                $itemClone = clone $item;
                $itemClone->setMargin($this);
                $blocsClone->add($itemClone);
            }

            $this->blocs = $blocsClone;
        }
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Marge';
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

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

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

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getBlocs(): Collection
    {
        return $this->blocs;
    }

    public function addBloc(Bloc $bloc): self
    {
        if (!$this->blocs->contains($bloc)) {
            $this->blocs->add($bloc);
            $bloc->setMargin($this);
        }

        return $this;
    }

    public function removeBloc(Bloc $bloc): void
    {
        $this->blocs->removeElement($bloc);
    }

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground(?string $background): self
    {
        $this->background = $background;

        return $this;
    }

    public function getBackgroundImg(): ?Media
    {
        return $this->backgroundImg;
    }

    public function setBackgroundImg(?Media $backgroundImg): self
    {
        $this->backgroundImg = $backgroundImg;

        return $this;
    }
}
