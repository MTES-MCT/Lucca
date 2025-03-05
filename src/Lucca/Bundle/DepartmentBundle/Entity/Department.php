<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */


namespace Lucca\Bundle\DepartmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\CoreBundle\Entity\ToggleableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

use Lucca\Bundle\DepartmentBundle\Repository\DepartmentRepository;

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

    #[ORM\Column(type: 'string', unique: true)]
    private string $code;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment;


    /************************************************************************** Custom function ************************************************************************/

    public function __toString(): string
    {
        return $this->name;
    }

    public function formLabel(): string
    {
        return $this->name . ' (' . $this->code . ')';
    }

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
