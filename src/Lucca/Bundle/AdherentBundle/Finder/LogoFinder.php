<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Finder;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

class LogoFinder
{
    private ?string $officialLogo;

    public function __construct()
    {
        $this->officialLogo = SettingManager::get('setting.pdf.logo.name');
    }

    /**
     * Define specific logo who was used
     */
    public function findLogo(Adherent $adherent): null|string|Media
    {
        if ($adherent->getLogo()) {
            return $adherent->getLogo();
        }

        if ($this->officialLogo) {
            return $this->officialLogo;
        }

        return null;
    }
}
