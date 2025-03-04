<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

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
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "id", type: "integer")]
    private ?int $id;

    #[ORM\Column(name: "num", type: "smallint")]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private int $num;

    #[ORM\Column(name: "name", type: "string", length: 50)]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 50, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private string $name;

    #[ORM\Column(name: "category", type: "string", length: 30, nullable: true)]
    #[Assert\Type(type: "string", message: "constraint.type")]
    #[Assert\Length(min: 2, max: 30, minMessage: "constraint.length.min", maxMessage: "constraint.length.max")]
    private ?string $category = null;

    #[ORM\Column(name: "description", type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Proposal::class, mappedBy: "tag", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $proposals;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Tag constructor
     */
    public function __construct()
    {
        $this->proposals = new ArrayCollection();
    }

    /**
     * Add proposal
     * Override function
     *
     * @param Proposal $proposal
     * @return Tag
     */
    public function addProposal(Proposal $proposal): static
    {
        $this->proposals[] = $proposal;
        $proposal->setTag($this);

        return $this;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Mot clé';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set num
     *
     * @param integer $num
     *
     * @return Tag
     */
    public function setNum($num): static
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer
     */
    public function getNum(): int
    {
        return $this->num;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Tag
     */
    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Tag
     */
    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Tag
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Remove proposal
     *
     * @param Proposal $proposal
     */
    public function removeProposal(Proposal $proposal): void
    {
        $this->proposals->removeElement($proposal);
    }

    /**
     * Get proposals
     *
     * @return Collection
     */
    public function getProposals(): ArrayCollection|Collection
    {
        return $this->proposals;
    }
}
