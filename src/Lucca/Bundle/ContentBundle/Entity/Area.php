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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\ContentBundle\Repository\AreaRepository;
use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: AreaRepository::class)]
#[ORM\Table(name: 'lucca_content_area')]
class Area implements LoggableInterface
{
    use ToggleableTrait, TimestampableTrait;

    /** STATE constants */
    const POSI_CONTENT = 'choice.position.content';
    const POSI_FOOTER = 'choice.position.footer';
    const POSI_ADMIN = 'choice.position.admin';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: SubArea::class, mappedBy: 'area', cascade: ['persist', 'remove'])]
    private Collection $subareas;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 30)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    private string $position;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->subareas = new ArrayCollection();

        $this->setPosition($this::POSI_CONTENT);
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Zone';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubareas(): Collection
    {
        return $this->subareas;
    }

    public function addSubarea(SubArea $subarea): self
    {
        if (!$this->subareas->contains($subarea)) {
            $this->subareas[] = $subarea;
            $subarea->setArea($this);
        }

        return $this;
    }

    public function removeSubarea(SubArea $subarea): void
    {
        $this->subareas->removeElement($subarea);
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

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }
}
