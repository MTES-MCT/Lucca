<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\DepartmentBundle\Repository\DepartmentRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ORM\Table(name: 'lucca_department')]
class Department implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;
    use ToggleableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(unique: true)]
    private string $code;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;


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
