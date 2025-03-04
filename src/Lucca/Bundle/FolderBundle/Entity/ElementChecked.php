<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\FolderBundle\Repository\ElementCheckedRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\MediaBundle\Entity\MediaAsyncInterface;

#[ORM\Table(name: "lucca_minute_folder_element")]
#[ORM\Entity(repositoryClass: ElementCheckedRepository::class)]
class ElementChecked implements LoggableInterface, MediaAsyncInterface
{
    use TimestampableTrait;

    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Folder::class, inversedBy: "elements")]
    #[ORM\JoinColumn(nullable: false)]
    private Folder $folder;

    #[ORM\Column(name: "name", type: "string", length: 255)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 255, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $name;

    #[ORM\Column(name: "state", type: "boolean")]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private bool $state = false;

    #[ORM\Column(name: "position", type: "smallint", nullable: true)]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private ?int $position = null;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Media $image = null;

    #[ORM\Column(name: "comment", type: "text", nullable: true)]
    private ?string $comment = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * ElementChecked constructor.
     *
     * @param Folder|null $folder
     * @param Element|null $element
     */
    public function __construct(?Folder $folder = null, ?Element $element = null)
    {
        if ($folder)
            $this->setFolder($folder);

        if ($element)
            $this->setName($element->getName());
    }

    /**
     * Set media by asynchronous method.
     *
     * @param Media|null $media
     *
     * @return ElementChecked
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
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Element validé';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ElementChecked
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set state.
     *
     * @param bool $state
     *
     * @return ElementChecked
     */
    public function setState(bool $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return bool
     */
    public function getState(): bool
    {
        return $this->state;
    }

    /**
     * Set position.
     *
     * @param int $position
     *
     * @return ElementChecked
     */
    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return ElementChecked
     */
    public function setComment(?string $comment = null): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set folder.
     *
     * @param Folder $folder
     *
     * @return ElementChecked
     */
    public function setFolder(Folder $folder): static
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder.
     *
     * @return Folder
     */
    public function getFolder(): Folder
    {
        return $this->folder;
    }

    /**
     * Set image.
     *
     * @param Media $image
     *
     * @return ElementChecked
     */
    public function setImage(Media $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return Media|null
     */
    public function getImage(): ?Media
    {
        return $this->image;
    }
}
