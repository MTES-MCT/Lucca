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

use Lucca\Bundle\ContentBundle\Repository\PageRepository;
use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MediaBundle\Entity\{Media, MediaListAsyncInterface};
use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'lucca_content_page')]
class Page implements LoggableInterface, MediaListAsyncInterface
{
    /** Traits */
    use ToggleableTrait, TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: SubArea::class, inversedBy: 'pages')]
    #[ORM\JoinColumn(nullable: false)]
    private SubArea $subarea;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'pages')]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 100)]
    private string $slug;

    #[ORM\Column(length: 40, nullable: true)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 40, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $icon = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $link;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private int $position;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Department $department;

    // 65535 is the maximum length of a TEXT field in MySQL
    #[ORM\Column(type: Types::TEXT, length: 65535, nullable: true)]
    private ?string $content = null;

    #[ORM\JoinTable(name: 'lucca_content_page_linked_media')]
    #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'media_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Media::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $mediasLinked;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->mediasLinked = new ArrayCollection();
    }

    public function addAsyncMedia(Media $media, string $vars = null): MediaListAsyncInterface
    {
       return  $this->addMediasLinked($media);
    }

    public function removeAsyncMedia(Media $media, string $vars = null): bool
    {
        return $this->removeMediasLinked($media);
    }

    public function getAsyncMedias(): Collection
    {
        return $this->getMediasLinked();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Page';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubarea(): SubArea
    {
        return $this->subarea;
    }

    public function setSubarea(SubArea $subarea): self
    {
        $this->subarea = $subarea;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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

    public function getMediasLinked(): Collection
    {
        return $this->mediasLinked;
    }

    public function addMediasLinked(Media $mediasLinked): self
    {
        if (!$this->mediasLinked->contains($mediasLinked)) {
            $this->mediasLinked[] = $mediasLinked;
        }

        return $this;
    }

    public function removeMediasLinked(Media $mediasLinked): bool
    {
        return $this->mediasLinked->removeElement($mediasLinked);
    }
}
