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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\CoreBundle\Entity\ToggleableTrait;
use Lucca\Bundle\FolderBundle\Repository\NatinfRepository;
use Lucca\Bundle\LogBundle\Entity\LogInterface;

/**
 * Natinf
 *
 * @package Lucca\Bundle\FolderBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
#[ORM\Table(name: "lucca_natinf")]
#[ORM\Entity(repositoryClass: NatinfRepository::class)]
class Natinf implements LogInterface
{
    use ToggleableTrait, TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "id", type: "integer")]
    private ?int $id;

    #[ORM\Column(name: "num", type: "integer")]
    #[Assert\NotNull(message: "constraint.not_null")]
    #[Assert\Type(type: "int", message: "constraint.type")]
    private int $num;

    #[ORM\Column(name: "qualification", type: "string", length: 255)]
    #[Assert\NotNull(message: "constraint.not_null")]
    private string $qualification;

    #[ORM\Column(name: "definedBy", type: "string", length: 255)]
    #[Assert\NotNull(message: "constraint.not_null")]
    private string $definedBy;

    #[ORM\Column(name: "repressedBy", type: "string", length: 255)]
    #[Assert\NotNull(message: "constraint.not_null")]
    private string $repressedBy;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: "lucca_natinf_linked_tag",
        joinColumns: [new ORM\JoinColumn(name: "natinf_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "tag_id", referencedColumnName: "id")]
    )]
    private $tags;

    #[ORM\ManyToOne(targetEntity: Natinf::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Natinf $parent = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Natinf constructor
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Label use on form
     *
     * @return string
     */
    public function getFormLabel(): string
    {
        return $this->getNum() . ' / ' . $this->getQualification();
    }

    /**
     * Check if tag exist in array
     * @param Tag $tag
     * @return bool
     */
    public function hasTag(Tag $tag): bool
    {
        if ($this->getTags()) {
            foreach ($this->getTags() as $element) {
                if ($element === $tag)
                    return true;
            }
        }

        return false;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Natinf';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id
     *
     * @return ?integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set num
     *
     * @param integer $num
     *
     * @return Natinf
     */
    public function setNum(int $num): static
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
     * Set qualification
     *
     * @param string $qualification
     *
     * @return Natinf
     */
    public function setQualification(string $qualification): static
    {
        $this->qualification = $qualification;

        return $this;
    }

    /**
     * Get qualification
     *
     * @return string
     */
    public function getQualification(): string
    {
        return $this->qualification;
    }

    /**
     * Set definedBy
     *
     * @param string $definedBy
     *
     * @return Natinf
     */
    public function setDefinedBy(string $definedBy): static
    {
        $this->definedBy = $definedBy;

        return $this;
    }

    /**
     * Get definedBy
     *
     * @return string
     */
    public function getDefinedBy(): string
    {
        return $this->definedBy;
    }

    /**
     * Set repressedBy
     *
     * @param string $repressedBy
     *
     * @return Natinf
     */
    public function setRepressedBy(string $repressedBy): static
    {
        $this->repressedBy = $repressedBy;

        return $this;
    }

    /**
     * Get repressedBy
     *
     * @return string
     */
    public function getRepressedBy(): string
    {
        return $this->repressedBy;
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
     * Add tag
     *
     * @param Tag $tag
     *
     * @return Natinf
     */
    public function addTag(Tag $tag): static
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return ArrayCollection|Collection
     */
    public function getTags(): ArrayCollection|Collection
    {
        return $this->tags;
    }

    /**
     * Set parent
     *
     * @param Natinf|null $parent
     *
     * @return Natinf
     */
    public function setParent(Natinf $parent = null): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Natinf|null
     */
    public function getParent(): Natinf|null
    {
        return $this->parent;
    }
}
