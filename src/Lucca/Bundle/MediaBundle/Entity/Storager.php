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

use Lucca\Bundle\CoreBundle\Entity\ToggleableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MediaBundle\Repository\StoragerRepository;

#[ORM\Entity(repositoryClass: StoragerRepository::class)]
#[ORM\Table(name: 'lucca_media_storager')]
class Storager implements LoggableInterface
{
    /** Traits */
    use ToggleableTrait;

    /** Default name used by service initialization */
    const DEFAULT_NAME = 'Default';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 150, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 50)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    private string $serviceFolderNaming;

    #[ORM\Column(length: 50)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    private string $serviceMediaNaming;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\JoinTable(name: 'lucca_media_storager_linked_folder')]
    #[ORM\JoinColumn(name: 'storager_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'folder_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Folder::class, cascade: ['persist', 'remove'])]
    private Collection $folders;

    /************************************************************************* Custom functions *****************************************************************************/

    public function __construct()
    {
        $this->folders = new ArrayCollection();
    }

    /**
     * Label used on Form Entity field
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
        return 'Storager';
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getServiceFolderNaming(): string
    {
        return $this->serviceFolderNaming;
    }

    public function setServiceFolderNaming(string $serviceFolderNaming): self
    {
        $this->serviceFolderNaming = $serviceFolderNaming;

        return $this;
    }

    public function getServiceMediaNaming(): string
    {
        return $this->serviceMediaNaming;
    }

    public function setServiceMediaNaming(string $serviceMediaNaming): self
    {
        $this->serviceMediaNaming = $serviceMediaNaming;

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

    public function getFolders(): Collection
    {
        return $this->folders;
    }

    public function addFolder(Folder $folder): self
    {
        if (!$this->folders->contains($folder)) {
            $this->folders[] = $folder;
        }

        return $this;
    }

    public function removeFolder(Folder $folder): bool
    {
        return $this->folders->removeElement($folder);
    }
}
