<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Entity;

use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\{ToggleableTrait, TimestampableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MediaBundle\Repository\CategoryRepository;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'lucca_media_category')]
class Category implements LoggableInterface
{
    /** Traits */
    use ToggleableTrait, TimestampableTrait;

    /** Default name used by service initialization */
    const DEFAULT_NAME = 'Default';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 50)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $icon;

    // 65535 is the maximum length of a TEXT field in MySQL
    #[ORM\Column(type: Types::TEXT, length: 65535, nullable: true)]
    private ?string $description = null;

    #[ORM\JoinTable(name: 'lucca_media_category_linked_meta_data_model')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'meta_data_model_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: MetaDataModel::class, cascade: ['persist', 'remove'])]
    private Collection $metasDatasModels;

    #[ORM\JoinTable(name: 'lucca_media_category_linked_extension')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'extension_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Extension::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $extensions;

    #[ORM\ManyToOne(targetEntity: Storager::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Storager $storager;

    /************************************************************************* Custom functions *****************************************************************************/

    public function __construct()
    {
        $this->metasDatasModels = new ArrayCollection();
        $this->extensions = new ArrayCollection();

        $this->icon = "fas fa-layer-group";
    }

    /**
     * Check if Category has a specific Extension
     * Check by Name or Value
     */
    public function hasExtension($p_extensionMeta): Extension|false
    {
        foreach ($this->getExtensions() as $extension) {
            if ($p_extensionMeta === $extension->getName() or $p_extensionMeta === $extension->getValue()) {
                return $extension;
            }
        }

        return false;
    }

    /**
     * Label displayed on form
     */
    public function getFormLabel(): string
    {
        return $this->getName();
    }

    /**
     * Log name
     */
    public function getLogName(): string
    {
        return 'Category';
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

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

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

    public function getMetasDatasModels(): Collection
    {
        return $this->metasDatasModels;
    }

    public function addMetasDatasModel(MetaDataModel $metasDatasModel): self
    {
        if (!$this->metasDatasModels->contains($metasDatasModel)) {
            $this->metasDatasModels->add($metasDatasModel);
        }

        return $this;
    }

    public function removeMetasDatasModel(MetaDataModel $metasDatasModel): bool
    {
        return $this->metasDatasModels->removeElement($metasDatasModel);
    }

    public function getExtensions(): Collection
    {
        return $this->extensions;
    }

    public function addExtension(Extension $extension): self
    {
        if (!$this->extensions->contains($extension)) {
            $this->extensions->add($extension);
        }

        return $this;
    }

    public function removeExtension(Extension $extension): bool
    {
        return $this->extensions->removeElement($extension);
    }

    public function getStorager(): Storager
    {
        return $this->storager;
    }

    public function setStorager(Storager $storager): self
    {
        $this->storager = $storager;

        return $this;
    }
}
