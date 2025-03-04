<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\CoreBundle\Entity\ToggleableTrait;
use Lucca\Bundle\FolderBundle\Repository\TagRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Table(name: "lucca_tag")]
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag implements LoggableInterface
{
    use ToggleableTrait, TimestampableTrait;

    const CATEGORY_NATURE = 'choice.category.nature';
    const CATEGORY_TOWN = 'choice.category.town';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private int $num;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $name;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 30, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Proposal::class, mappedBy: "tag", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $proposals;

    /************************************************************************ Custom functions ************************************************************************/

    public function __construct()
    {
        $this->proposals = new ArrayCollection();
    }

    /**
     * @ihneritdoc
     */
    public function getLogName(): string
    {
        return 'Mot clé';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNum(): int
    {
        return $this->num;
    }

    public function setNum(int $num): self
    {
        $this->num = $num;

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProposals(): Collection
    {
        return $this->proposals;
    }

    public function addProposal(Proposal $proposal): self
    {
        if (!$this->proposals->contains($proposal)) {
            $this->proposals[] = $proposal;
            $proposal->setTag($this);
        }

        return $this;
    }

    public function removeProposal(Proposal $proposal): void
    {
        $this->proposals->removeElement($proposal);
    }
}
