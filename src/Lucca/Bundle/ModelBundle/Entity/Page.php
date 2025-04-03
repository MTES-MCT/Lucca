<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ModelBundle\Repository\PageRepository;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'lucca_model_page')]
class Page implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    /** MARG_UNIT constants */
    const MARG_UNIT_PERCENT = 'choice.marginUnit.percent';
    const MARG_UNIT_PX = 'choice.marginUnit.px';

    /** FONTS constants */
    const FONT_MARIANNE_MEDIUM = 'choice.font.marianne.medium';
    const FONT_MARIANNE_LIGHT = 'choice.font.marianne.light';
    const FONT_MARIANNE_REGULAR = 'choice.font.marianne.regular';
    const FONT_MARIANNE_THIN = 'choice.font.marianne.thin';
    const FONT_MARIANNE_EXTRABOLD = 'choice.font.marianne.extrabold';
    const FONT_MARIANNE_BOLD = 'choice.font.marianne.bold';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice(callback: 'getMargUnits', message: 'constraint.layout.initiatingStructure')]
    private ?string $marginUnit = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice([
        Page::FONT_MARIANNE_MEDIUM,
        Page::FONT_MARIANNE_LIGHT,
        Page::FONT_MARIANNE_REGULAR,
        Page::FONT_MARIANNE_THIN,
        Page::FONT_MARIANNE_EXTRABOLD,
        Page::FONT_MARIANNE_BOLD,
    ], message: 'constraint.layout.initiatingStructure')]
    private ?string $font = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    private ?string $color = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 3, max: 20, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $background = null;

    #[ORM\Column(name: 'css_inline', type: Types::TEXT, nullable: true)]
    private ?string $cssInline = null;

    /// ------------------------------------------------------------
    /// This fields above are use only if Margins are not used :
    /// $headerSize , $footerSize , $leftSize , $rightSize
    ///
    /// else we use :
    /// $marginTop , $marginBottom , $marginLeft , $marginRight
    /// ------------------------------------------------------------

    #[ORM\Column(name: 'header_size', type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $headerSize = 0;

    #[ORM\Column(name: 'footer_size', type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $footerSize = 0;

    #[ORM\Column(name: 'left_size', type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $leftSize = 0;

    #[ORM\Column(name: 'right_size', type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: 'int', message: 'constraint.type')]
    private ?int $rightSize = 0;

    #[ORM\OneToOne(targetEntity: Margin::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Margin $marginTop = null;

    #[ORM\OneToOne(targetEntity: Margin::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Margin $marginBottom = null;

    #[ORM\OneToOne(targetEntity: Margin::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Margin $marginLeft = null;

    #[ORM\OneToOne(targetEntity: Margin::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Margin $marginRight = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Return array containing all margin unit
     */
    public static function getMargUnits(): array
    {
        return [
//            self::MARG_UNIT_PERCENT => self::MARG_UNIT_PERCENT, // wip bad calcul
            self::MARG_UNIT_PX => self::MARG_UNIT_PX,
        ];
    }

    public function getHeightTop(): int
    {
        return $this->marginTop !== null ? $this->marginTop->getHeight() : $this->headerSize ?? 0;
    }

    public function getHeightBottom(): int
    {
        return $this->marginBottom !== null ? $this->marginBottom->getHeight() : $this->footerSize ?? 0;
    }

    public function getWidthLeft(): int
    {
        return $this->marginLeft !== null ? $this->marginLeft->getWidth() : $this->leftSize ?? 0;
    }

    public function getWidthRight(): int
    {
        return $this->marginRight !== null ? $this->marginRight->getWidth() : $this->rightSize ?? 0;
    }

    public function __clone() {
        if ($this->id) {
            $this->setId(null);

            if ($this->marginBottom) {
                $this->marginBottom = clone $this->marginBottom;
            }

            if ($this->marginLeft) {
                $this->marginLeft = clone $this->marginLeft;
            }

            if ($this->marginRight) {
                $this->marginRight = clone $this->marginRight;
            }

            if ($this->marginTop) {
                $this->marginTop = clone $this->marginTop;
            }
        }
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

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getMarginUnit(): ?string
    {
        return $this->marginUnit;
    }

    public function setMarginUnit(?string $marginUnit): self
    {
        $this->marginUnit = $marginUnit;

        return $this;
    }

    public function getFont(): ?string
    {
        return $this->font;
    }

    public function setFont(?string $font): self
    {
        $this->font = $font;

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

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground(?string $background): self
    {
        $this->background = $background;

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

    public function getMarginTop(): ?Margin
    {
        return $this->marginTop;
    }

    public function setMarginTop(?Margin $marginTop): self
    {
        $this->marginTop = $marginTop;

        return $this;
    }

    public function getMarginBottom(): ?Margin
    {
        return $this->marginBottom;
    }

    public function setMarginBottom(?Margin $marginBottom): self
    {
        $this->marginBottom = $marginBottom;

        return $this;
    }

    public function getMarginLeft(): ?Margin
    {
        return $this->marginLeft;
    }

    public function setMarginLeft(?Margin $marginLeft): self
    {
        $this->marginLeft = $marginLeft;

        return $this;
    }

    public function getMarginRight(): ?Margin
    {
        return $this->marginRight;
    }

    public function setMarginRight(?Margin $marginRight): self
    {
        $this->marginRight = $marginRight;

        return $this;
    }
}
