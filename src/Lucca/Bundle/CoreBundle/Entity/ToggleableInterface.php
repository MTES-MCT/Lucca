<?php

namespace Lucca\Bundle\CoreBundle\Entity;

interface ToggleableInterface
{
    public function toggle();

    public function enable();

    public function disable();

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled);
}

