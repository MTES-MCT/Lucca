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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Table(name: "lucca_minute_folder_edition")]
#[ORM\Entity]
class FolderEdition implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\Type(type: "bool", message: "constraint.type")]
    private bool $folderEdited = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $folderVersion = null;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * @inheritdoc
     */
    public function getLogName(): string
    {
        return 'Procès verbal édition';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFolderEdited(): bool
    {
        return $this->folderEdited;
    }

    public function setFolderEdited(bool $folderEdited): self
    {
        $this->folderEdited = $folderEdited;

        return $this;
    }

    public function getFolderVersion(): ?string
    {
        return $this->folderVersion;
    }

    public function setFolderVersion(?string $folderVersion): self
    {
        $this->folderVersion = $folderVersion;

        return $this;
    }
}
