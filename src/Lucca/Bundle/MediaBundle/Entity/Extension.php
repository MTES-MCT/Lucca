<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\CoreBundle\Entity\ToggleableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;
use Lucca\Bundle\MediaBundle\Repository\ExtensionRepository;

#[ORM\Entity(repositoryClass: ExtensionRepository::class)]
#[ORM\Table(name: 'lucca_media_extension')]
class Extension implements LoggableInterface
{
    /** Traits */
    use ToggleableTrait;

    /** Default name used by service initialization */
    const DEFAULT_NAME = 'Default';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $name;

    #[ORM\Column(length: 50)]
    #[Assert\Type(type: 'string', message: 'constraint.type')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'constraint.length.min', maxMessage: 'constraint.length.max')]
    private string $value;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /************************************************************************* Custom functions *****************************************************************************/

    /**
     * Log name
     */
    public function getLogName(): string
    {
        return 'Extension';
    }

    /**
     * Check if extension is filled
     */
    public function isEmpty(): bool
    {
        if (!$this->getName() || !$this->getValue()) {
            return true;
        }

        return false;
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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
}
