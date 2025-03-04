<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\{TimestampableTrait, ToggleableTrait};
use Lucca\Bundle\FolderBundle\Repository\ProposalRepository;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Table(name: "lucca_proposal")]
#[ORM\Entity(repositoryClass: ProposalRepository::class)]
class Proposal implements LoggableInterface
{
    use ToggleableTrait, TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "id", type: "integer")]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Tag::class, inversedBy: "proposals")]
    #[ORM\JoinColumn(nullable: false)]
    private Tag $tag;

    #[ORM\Column(name: "sentence", type: "text")]
    #[Assert\NotNull(message: "constraint.not_null")]
    private string $sentence;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Proposition';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set sentence
     *
     * @param string $sentence
     *
     * @return Proposal
     */
    public function setSentence(string $sentence): static
    {
        $this->sentence = $sentence;

        return $this;
    }

    /**
     * Get sentence
     *
     * @return string
     */
    public function getSentence(): string
    {
        return $this->sentence;
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
     * Set tag
     *
     * @param Tag $tag
     *
     * @return Proposal
     */
    public function setTag(Tag $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return Tag
     */
    public function getTag(): Tag
    {
        return $this->tag;
    }
}
