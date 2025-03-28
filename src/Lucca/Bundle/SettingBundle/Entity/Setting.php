<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Entity;

use Exception;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

use Lucca\Bundle\SettingBundle\Repository\SettingRepository;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
#[ORM\Table(name: 'lucca_setting')]
class Setting implements LoggableInterface
{
    /** Setting TYPE constants */
    const TYPE_INTEGER = 'choice.setting.type.integer';
    const TYPE_FLOAT = 'choice.setting.type.float';
    const TYPE_PERCENT = 'choice.setting.type.percent';
    const TYPE_TEXT = 'choice.setting.type.text';
    const TYPE_TEXT_LARGE = 'choice.setting.type.text_large'; // displays a textarea
    const TYPE_BOOL = 'choice.setting.type.boolean';
    const TYPE_LIST = 'choice.setting.type.list';
    const TYPE_COLOR = 'choice.setting.type.color';

    /** Setting access type constant */
    const ACCESS_TYPE_SUPER_ADMIN = 'choice.setting.accessType.superAdmin';
    const ACCESS_TYPE_ADMIN = 'choice.setting.accessType.admin';

    /** Others declarations */
    const VALUES_SEPARATOR = ';';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    private string $name;

    #[ORM\Column]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Choice([
        Setting::TYPE_INTEGER,
        Setting::TYPE_FLOAT,
        Setting::TYPE_PERCENT,
        Setting::TYPE_TEXT,
        Setting::TYPE_BOOL,
        Setting::TYPE_LIST,
        Setting::TYPE_COLOR,
        Setting::TYPE_TEXT_LARGE,
    ], message: 'constraint.choice')]
    private string $type;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\Column]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Choice([
        Setting::ACCESS_TYPE_SUPER_ADMIN,
        Setting::ACCESS_TYPE_ADMIN,
    ], message: 'constraint.choice')]
    private string $accessType;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    private int $position;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $value = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $valuesAvailable = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @throws Exception
     */
    public function __construct($name, $type, $category, $accessType, $position, $value, $comment, $values = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->category = $category;
        $this->accessType = $accessType;
        $this->position = $position;
        $this->value = $value;
        $this->comment = $comment;

        /** Special case for List Type */
        if ($type == $this::TYPE_LIST) {
            /** For LIST Type at least 2 values are required  */
            if (is_null($values) || count($values) <= 1) {
                throw new Exception("Setting : Missing parameter values for type LIST");
            }

            $this->valuesAvailable = implode($this::VALUES_SEPARATOR, $values);
        }
    }

    /**
     * Get value.
     */
    public function getValue(): null|int|string|float
    {
        return self::castValue($this->type, $this->value);
    }

    /**
     * Cast value by type.
     */
    static public function castValue(string $type, ?string $value): null|int|string|float
    {
        switch ($type) {
            case self::TYPE_BOOL:
                $output = 1 === intval($value);
                break;
            case self::TYPE_INTEGER:
                $output = intval($value);
                break;
            case self::TYPE_PERCENT:
            case self::TYPE_FLOAT:
                $output = floatval($value);
                break;
            default:
            case self::TYPE_COLOR:
            case self::TYPE_LIST:
            case self::TYPE_TEXT:
            case self::TYPE_TEXT_LARGE:
                $output = $value;
                break;
        }

        return $output;
    }

    /**
     * Get values List.
     */
    public function getValuesAvailable(): false|array
    {
        return explode($this::VALUES_SEPARATOR, $this->valuesAvailable);
    }

    /**
     * Log name of this Class
     */
    public function getLogName(): string
    {
        return 'Setting';
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getAccessType(): string
    {
        return $this->accessType;
    }

    public function setAccessType(string $accessType): self
    {
        $this->accessType = $accessType;

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

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function setValuesAvailable(?string $valuesAvailable): self
    {
        $this->valuesAvailable = $valuesAvailable;

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
