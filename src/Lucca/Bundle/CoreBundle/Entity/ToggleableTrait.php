<?php

namespace Lucca\Bundle\CoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait ToggleableTrait
{
    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    protected bool $enabled = true;

    /********************************************* Trait functions *********************************************/

    public function toggle(): void
    {
        $this->enabled = !$this->enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}

