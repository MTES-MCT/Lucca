<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ModelBundle\Repository\ModelRepository;
use Lucca\Bundle\ParameterBundle\Entity\{Service, Intercommunal, Town};

#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\Table(name: 'lucca_model')]
class Model implements LoggableInterface
{
    /** Traits */
    use ToggleableTrait, TimestampableTrait;

    /** DOCUMENTS constants */
    const DOCUMENTS_CONVOCATION_LETTER = 'choice.documents.convocationLetter';
    const DOCUMENTS_ACCESS_LETTER = 'choice.documents.accessLetter';
    const DOCUMENTS_FOLDER = 'choice.documents.folder';
    const DOCUMENTS_JUDICIAL_LETTER = 'choice.documents.judicialLetter';
    const DOCUMENTS_DDTM_LETTER = 'choice.documents.ddtmLetter';
    const DOCUMENTS_OFFENDER_LETTER = 'choice.documents.offender';

    /** SIZE constants */
    const SIZE_A3 = 'choice.size.a3';
    const SIZE_A4 = 'choice.size.a4';
    const SIZE_A5 = 'choice.size.a5';

    /** ORIENTATION constants */
    const ORIENTATION_LANDSCAPE = 'choice.orientation.landscape';
    const ORIENTATION_PORTRAIT = 'choice.orientation.portrait';

    /** TYPE constants */
    const TYPE_ORIGIN = 'choice.type.origin';
    const TYPE_PRIVATE = 'choice.type.private';
    const TYPE_SHARED = 'choice.type.shared';

    /** LAYOUT constants */
    const LAYOUT_COVER = 'choice.layout.cover';
    const LAYOUT_SIMPLE = 'choice.layout.simple';

    /* size int pixel of */
    const SIZE = [
        self::SIZE_A5 => ['width' => 724, 'height' => 1082],
        self::SIZE_A4 => ['width' => 793, 'height' => 1121],
        self::SIZE_A3 => ['width' => 1403, 'height' => 1500],
    ];

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice(callback: 'getSizeChoice', message: 'constraint.size.initiatingStructure')]
    private string $size = self::SIZE_A4;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice(callback: 'getOrientationChoice', message: 'constraint.orientation.initiatingStructure')]
    private string $orientation = self::ORIENTATION_PORTRAIT;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice([
        Model::TYPE_ORIGIN,
        Model::TYPE_PRIVATE,
        Model::TYPE_SHARED,
    ], message: 'constraint.type.initiatingStructure')]
    private string $type;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice([
        Model::LAYOUT_COVER,
        Model::LAYOUT_SIMPLE,
    ], message: 'constraint.layout.initiatingStructure')]
    private string $layout;

    #[ORM\ManyToOne(targetEntity: Page::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Page $recto;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private Department $department;

    #[ORM\ManyToOne(targetEntity: Page::class, cascade: ['persist', 'remove'])]
    private ?Page $verso = null;

    #[ORM\Column(name: 'privacy', type: Types::JSON)]
    private array $documents = [];

    #[ORM\ManyToOne(targetEntity: Adherent::class)]
    private ?Adherent $owner = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    private ?Service $sharedService = null;

    #[ORM\ManyToOne(targetEntity: Intercommunal::class)]
    private ?Intercommunal $sharedIntercommunal = null;

    #[ORM\ManyToOne(targetEntity: Town::class)]
    private ?Town $sharedTown = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    private ?string $color = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 3, max: 20, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private ?string $background = null;

    #[ORM\Column(length: 50, options: ['default' => Page::FONT_MARIANNE_REGULAR])]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Choice([
        Page::FONT_MARIANNE_MEDIUM,
        Page::FONT_MARIANNE_LIGHT,
        Page::FONT_MARIANNE_REGULAR,
        Page::FONT_MARIANNE_THIN,
        Page::FONT_MARIANNE_EXTRABOLD,
        Page::FONT_MARIANNE_BOLD,
    ], message: 'constraint.layout.initiatingStructure')]
    private string $font = Page::FONT_MARIANNE_REGULAR;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * wip only A4 work because compute for other size doesn't work
     */
    public static function getSizeChoice(): array
    {
        return [
//            Model::SIZE_A3 => Model::SIZE_A3,
            Model::SIZE_A4 => Model::SIZE_A4,
//            Model::SIZE_A5 => Model::SIZE_A5,
        ];
    }

    /**
     * wip only PORTRAIT work because compute for other orientation doesn't work
     */
    public static function getOrientationChoice(): array
    {
        return [
            Model::ORIENTATION_PORTRAIT => Model::ORIENTATION_PORTRAIT,
//            Model::ORIENTATION_LANDSCAPE => Model::ORIENTATION_LANDSCAPE,
        ];
    }

    /**
     * Return document than can be used in model, useful in command to init model
     */
    public static function getDocumentsChoice(): array
    {
        return [
            Model::DOCUMENTS_ACCESS_LETTER => Model::DOCUMENTS_ACCESS_LETTER,
            Model::DOCUMENTS_CONVOCATION_LETTER => Model::DOCUMENTS_CONVOCATION_LETTER,
            Model::DOCUMENTS_FOLDER => Model::DOCUMENTS_FOLDER,
            Model::DOCUMENTS_DDTM_LETTER => Model::DOCUMENTS_DDTM_LETTER,
            Model::DOCUMENTS_JUDICIAL_LETTER => Model::DOCUMENTS_JUDICIAL_LETTER,
            Model::DOCUMENTS_OFFENDER_LETTER => Model::DOCUMENTS_OFFENDER_LETTER,
        ];
    }

    /**
     * return max Height in function of orientation and document type
     */
    public function getMaxHeight(): int
    {
        if ($this->orientation == self::ORIENTATION_PORTRAIT) {
            /* if this size is not null get constant height else 0 */
            return $this->getSize() !== null ? self::SIZE[$this->getSize()]["height"] : 0;
        }

        if ($this->orientation == self::ORIENTATION_LANDSCAPE) {
            /* if this size is not null get constant height else 0 */
            return self::SIZE[$this->getSize()]["width"];
        }

        return 0;
    }

    /**
     * return max Width in function of orientation and document type
     */
    public function getMaxWidth(): int
    {
        if ($this->orientation == self::ORIENTATION_PORTRAIT) {
            /* if this size is not null get constant height else 0 */
            return $this->getSize() !== null ? self::SIZE[$this->getSize()]["width"] : 0;
        }

        if ($this->orientation == self::ORIENTATION_LANDSCAPE) {
            /* if this size is not null get constant height else 0 */
            return self::SIZE[$this->getSize()]["height"];
        }

        return 0;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);

            /** Check if verso is not null before clone */
            if ($this->verso !== null) {
                $this->verso = clone $this->verso;
            }

            $this->recto = clone $this->recto;
        }
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Modèle';
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

    public function getSize(): string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getOrientation(): string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function getRecto(): Page
    {
        return $this->recto;
    }

    public function setRecto(Page $recto): self
    {
        $this->recto = $recto;

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

    public function getVerso(): ?Page
    {
        return $this->verso;
    }

    public function setVerso(?Page $verso): self
    {
        $this->verso = $verso;

        return $this;
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function setDocuments(array $documents): self
    {
        $this->documents = $documents;

        return $this;
    }

    public function getOwner(): ?Adherent
    {
        return $this->owner;
    }

    public function setOwner(?Adherent $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getSharedService(): ?Service
    {
        return $this->sharedService;
    }

    public function setSharedService(?Service $sharedService): self
    {
        $this->sharedService = $sharedService;

        return $this;
    }

    public function getSharedIntercommunal(): ?Intercommunal
    {
        return $this->sharedIntercommunal;
    }

    public function setSharedIntercommunal(?Intercommunal $sharedIntercommunal): self
    {
        $this->sharedIntercommunal = $sharedIntercommunal;

        return $this;
    }

    public function getSharedTown(): ?Town
    {
        return $this->sharedTown;
    }

    public function setSharedTown(?Town $sharedTown): self
    {
        $this->sharedTown = $sharedTown;

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

    public function getFont(): string
    {
        return $this->font;
    }

    public function setFont(string $font): self
    {
        $this->font = $font;

        return $this;
    }
}
