<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Lucca\Bundle\CoreBundle\Entity\TimestampableTrait;
use Lucca\Bundle\LogBundle\Entity\LoggableInterface;

#[ORM\Table(name: 'lucca_minute_control_edition')]
#[ORM\Entity]
class ControlEdition implements LoggableInterface
{
    /** Traits */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Control::class, inversedBy: 'editions')]
    #[ORM\JoinColumn(nullable: false)]
    private Control $control;

    #[ORM\ManyToOne(targetEntity: Human::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Human $human;

    #[ORM\Column]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $convocationEdited = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $letterConvocation = null;

    #[ORM\Column]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $accessEdited = false;

    #[ORM\Column(name: 'letterAccess', type: Types::TEXT, nullable: true)]
    private ?string $letterAccess = null;

    /************************************************************************ Custom functions ************************************************************************/

    public function getLogName(): string
    {
        return 'Contrôle édition';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setConvocationEdited(bool $convocationEdited): self
    {
        $this->convocationEdited = $convocationEdited;

        return $this;
    }

    public function getConvocationEdited(): bool
    {
        return $this->convocationEdited;
    }

    public function setLetterConvocation(?string $letterConvocation): self
    {
        $this->letterConvocation = $letterConvocation;

        return $this;
    }

    public function getLetterConvocation(): ?string
    {
        return $this->letterConvocation;
    }

    public function setAccessEdited(bool $accessEdited): self
    {
        $this->accessEdited = $accessEdited;

        return $this;
    }

    public function getAccessEdited(): bool
    {
        return $this->accessEdited;
    }

    public function setLetterAccess(?string $letterAccess): self
    {
        $this->letterAccess = $letterAccess;

        return $this;
    }

    public function getLetterAccess(): ?string
    {
        return $this->letterAccess;
    }

    public function setControl(Control $control): self
    {
        $this->control = $control;

        return $this;
    }

    public function getControl(): Control
    {
        return $this->control;
    }

    public function setHuman(Human $human): self
    {
        $this->human = $human;

        return $this;
    }

    public function getHuman(): Human
    {
        return $this->human;
    }
}
