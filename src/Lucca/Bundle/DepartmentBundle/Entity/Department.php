<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Entity;

use DateTime;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\DepartmentBundle\Repository\DepartmentRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ORM\Table(name: 'lucca_department')]
#[UniqueEntity(fields: ['code'], message: 'constraint.unique.department.code', errorPath: 'code')]
class Department implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;
    use ToggleableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private string $code;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $showInHomePage = true;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $lastSyncSetting = null;

    /************************************************************************** Custom function ************************************************************************/

    public function __toString(): string
    {
        return $this->name;
    }

    public function formLabel(): string
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return "Département";
    }

    /************************************************************************** Automatic Getters & Setters ************************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
    
    public function isShowInHomePage(): bool
    {
        return $this->showInHomePage;
    }

    public function setShowInHomePage(bool $showInHomePage): self
    {
        $this->showInHomePage = $showInHomePage;

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

    public function getLastSyncSetting(): ?DateTime
    {
        return $this->lastSyncSetting;
    }

    public function setLastSyncSetting(?DateTime $lastSyncSetting): void
    {
        $this->lastSyncSetting = $lastSyncSetting;
    }
}
