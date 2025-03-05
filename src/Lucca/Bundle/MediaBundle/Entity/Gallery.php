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
use Lucca\Bundle\MediaBundle\Repository\GalleryRepository;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[ORM\Table(name: 'lucca_media_gallery')]
class Gallery implements LoggableInterface , MediaAsyncInterface, MediaListAsyncInterface
{
    /** Traits */
    use ToggleableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 150, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['persist', 'remove'])]
    private ?Media $defaultMedia = null;

    #[ORM\JoinTable(name: 'lucca_media_gallery_linked_media')]
    #[ORM\JoinColumn(name: 'gallery_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'media_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Media::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $medias;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /************************************************************************* Custom functions *****************************************************************************/

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Gallery';
    }

    /**
     * @inheritdoc
     */
    public function setAsyncMedia(?Media $media = null): self
    {
        $this->setDefaultMedia($media);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAsyncMedia(): ?Media
    {
        return $this->getDefaultMedia();
    }

    /**
     * @inheritdoc
     */
    public function addAsyncMedia(Media $media, string $vars = null): self
    {
        return $this->addMedia($media);
    }

    /**
     * @inheritdoc
     */
    public function removeAsyncMedia(Media $media, string $vars = null): bool
    {
        return $this->removeMedia($media);
    }

    /**
     * @inheritdoc
     */
    public function getAsyncMedias(): Collection
    {
        return $this->getMedias();
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

    public function getDefaultMedia(): ?Media
    {
        return $this->defaultMedia;
    }

    public function setDefaultMedia(?Media $defaultMedia): self
    {
        $this->defaultMedia = $defaultMedia;

        return $this;
    }

    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    public function removeMedia(Media $media): bool
    {
        return $this->medias->removeElement($media);
    }
}
