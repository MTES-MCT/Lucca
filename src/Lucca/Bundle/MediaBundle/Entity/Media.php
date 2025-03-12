<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MediaBundle\Repository\MediaRepository;
use Lucca\Bundle\UserBundle\Entity\User;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ORM\Table(name: 'lucca_media')]
class Media implements LoggableInterface
{
    /** Traits */
    use ToggleableTrait, TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $nameOriginal;

    #[ORM\Column]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $nameCanonical;

    #[ORM\Column]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $public = false;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $filePath = null;

    #[ORM\Column(length:150, nullable: true)]
    private ?string $copyright = null;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $mimeType;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: Folder::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Folder $folder;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\JoinTable(name: 'lucca_media_linked_meta_data')]
    #[ORM\JoinColumn(name: 'media_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'meta_data_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\OneToMany(targetEntity: MetaData::class, mappedBy: 'media', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $metas;

    /************************************************************************* Custom functions *****************************************************************************/

    public function __construct()
    {
        $this->metas = new ArrayCollection();
    }

    /**
     * Log name
     */
    public function getLogName(): string
    {
        return 'Media';
    }

    /**
     * Test if a Media is empty
     */
    public function isEmpty(): bool
    {
        if ($this->getName() && $this->getFilePath()) {
            return false;
        }

        return true;
    }

    public function addMeta(MetaData $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas->add($meta);
            $meta->setMedia($this);
        }

        return $this;
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

    public function getNameOriginal(): string
    {
        return $this->nameOriginal;
    }

    public function setNameOriginal(string $nameOriginal): self
    {
        $this->nameOriginal = $nameOriginal;

        return $this;
    }

    public function getNameCanonical(): string
    {
        return $this->nameCanonical;
    }

    public function setNameCanonical(string $nameCanonical): self
    {
        $this->nameCanonical = $nameCanonical;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setCopyright(?string $copyright): self
    {
        $this->copyright = $copyright;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getFolder(): Folder
    {
        return $this->folder;
    }

    public function setFolder(Folder $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getMetas(): Collection
    {
        return $this->metas;
    }

    public function removeMeta(MetaData $meta) :bool
    {
        return $this->metas->removeElement($meta);
    }
}
