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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\FolderBundle\Repository\ElementCheckedRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MediaBundle\Entity\{Media, MediaAsyncInterface};

#[ORM\Table(name: "lucca_minute_folder_element")]
#[ORM\Entity(repositoryClass: ElementCheckedRepository::class)]
class ElementChecked implements LoggableInterface, MediaAsyncInterface
{
    use TimestampableTrait;

    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Folder::class, inversedBy: "elements")]
    #[ORM\JoinColumn(nullable: false)]
    private Folder $folder;

    #[ORM\Column]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 255, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $name;

    #[ORM\Column]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private bool $state = false;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $position = null;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ["persist"])]
    private ?Media $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct(?Folder $folder = null, ?Element $element = null)
    {
        if ($folder) {
            $this->setFolder($folder);
        }

        if ($element) {
            $this->setName($element->getName());
        }
    }

    /**
     * Set media by asynchronous method.
     */
    public function setAsyncMedia(?Media $media = null): self
    {
        $this->setImage($media);

        return $this;
    }

    public function getAsyncMedia(): ?Media
    {
        return $this->getImage();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Element validé';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getState(): bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(?Media $image): self
    {
        $this->image = $image;

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
