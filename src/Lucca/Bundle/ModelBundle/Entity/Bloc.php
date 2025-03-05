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
use Lucca\Bundle\MediaBundle\Entity\{Media, MediaListAsyncInterface};
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ModelBundle\Repository\BlocRepository;

#[ORM\Entity(repositoryClass: BlocRepository::class)]
#[ORM\Table(name: 'lucca_model_bloc')]
class Bloc implements LoggableInterface, MediaListAsyncInterface
{
    /** Traits */
    use TimestampableTrait;

    /** TYPE_CONTENT constants */
    const TYPE_CONTENT_HTML = 'choice.typeContent.html';
    const TYPE_CONTENT_MEDIA = 'choice.typeContent.media';
    const TYPE_CONTENT_ADHERENT_LOGO = 'choice.typeContent.adherentLogo';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Margin::class, inversedBy: 'blocs')]
    #[ORM\JoinColumn(nullable: false)]
    private Margin $margin;

    #[ORM\Column]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private int $height;

    #[ORM\Column]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private int $width;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice([
        Bloc::TYPE_CONTENT_HTML,
        Bloc::TYPE_CONTENT_MEDIA,
        Bloc::TYPE_CONTENT_ADHERENT_LOGO,
    ], message: 'constraint.typeContent.initiatingStructure')]
    private string $typeContent;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Department $department;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $headerSize = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $footerSize = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $leftSize = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $rightSize = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['persist', 'remove'])]
    private ?Media $media = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 3, max: 20, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $color = null;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['persist', 'remove'])]
    private ?Media $backgroundImg = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cssInline = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Add media by asynchronous method.
     */
    public function addAsyncMedia(?Media $media, ?string $vars = null): self
    {
        return match ($vars) {
            'backgroundImg' => $this->setBackgroundImg($media),
            'media' => $this->setMedia($media),
            default => $this,
        };
    }

    /**
     * Remove media by asynchronous method.
     */
    public function removeAsyncMedia(?Media $media, string $vars = null): bool
    {
        match ($vars) {
            'backgroundImg' => $this->setBackgroundImg(null),
            'media' => $this->setMedia(null),
        };

        return true;
    }

    /**
     * Get medias by asynchronous method.
     * Get all medias of the entity and return an ArrayCollection
     */
    public function getAsyncMedias(): Collection
    {
        return new ArrayCollection([
            $this->getBackgroundImg(),
            $this->getMedia(),
        ]);
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
        }
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Bloc';
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

    public function getMargin(): Margin
    {
        return $this->margin;
    }

    public function setMargin(Margin $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

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

    public function getTypeContent(): string
    {
        return $this->typeContent;
    }

    public function setTypeContent(string $typeContent): self
    {
        $this->typeContent = $typeContent;

        return $this;
    }

    public function getHeaderSize(): ?int
    {
        return $this->headerSize;
    }

    public function setHeaderSize(?int $headerSize): self
    {
        $this->headerSize = $headerSize;

        return $this;
    }

    public function getFooterSize(): ?int
    {
        return $this->footerSize;
    }

    public function setFooterSize(?int $footerSize): self
    {
        $this->footerSize = $footerSize;

        return $this;
    }

    public function getLeftSize(): ?int
    {
        return $this->leftSize;
    }

    public function setLeftSize(?int $leftSize): self
    {
        $this->leftSize = $leftSize;

        return $this;
    }

    public function getRightSize(): ?int
    {
        return $this->rightSize;
    }

    public function setRightSize(?int $rightSize): self
    {
        $this->rightSize = $rightSize;

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

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

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

    public function getCssInline(): ?string
    {
        return $this->cssInline;
    }

    public function setCssInline(?string $cssInline): self
    {
        $this->cssInline = $cssInline;

        return $this;
    }
}
