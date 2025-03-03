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
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LogInterface;

/**
 * FolderEdition
 *
 * @ORM\Table(name="lucca_minute_folder_edition")
 * @ORM\Entity()
 *
 * @package Lucca\Bundle\FolderBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
#[ORM\Table(name: "lucca_minute_folder_edition")]
#[ORM\Entity]
class FolderEdition implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id;

    #[ORM\Column(name: "folderEdited", type: "boolean")]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private bool $folderEdited = false;

    #[ORM\Column(name: "folderVersion", type: "text", nullable: true)]
    private ?string $folderVersion = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName(): string
    {
        return 'Procès verbal édition';
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
     * Set folderEdited
     *
     * @param boolean $folderEdited
     *
     * @return FolderEdition
     */
    public function setFolderEdited(bool $folderEdited): static
    {
        $this->folderEdited = $folderEdited;

        return $this;
    }

    /**
     * Get folderEdited
     *
     * @return boolean
     */
    public function getFolderEdited(): bool
    {
        return $this->folderEdited;
    }

    /**
     * Set folderVersion
     *
     * @param string $folderVersion
     *
     * @return FolderEdition
     */
    public function setFolderVersion(string $folderVersion): static
    {
        $this->folderVersion = $folderVersion;

        return $this;
    }

    /**
     * Get folderVersion
     *
     * @return string
     */
    public function getFolderVersion(): ?string
    {
        return $this->folderVersion;
    }
}
