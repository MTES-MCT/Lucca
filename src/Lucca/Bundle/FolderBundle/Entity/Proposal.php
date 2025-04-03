<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\FolderBundle\Repository\ProposalRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

#[ORM\Table(name: "lucca_proposal")]
#[ORM\Entity(repositoryClass: ProposalRepository::class)]
class Proposal implements LoggableInterface
{
    use ToggleableTrait, TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tag::class, inversedBy: "proposals")]
    #[ORM\JoinColumn(nullable: false)]
    private Tag $tag;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull(message: "constraint.not_null")]
    private string $sentence;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    /** TODO: set nullable for migration */
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Proposition';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function setTag(Tag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getSentence(): string
    {
        return $this->sentence;
    }

    public function setSentence(string $sentence): self
    {
        $this->sentence = $sentence;

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
}
