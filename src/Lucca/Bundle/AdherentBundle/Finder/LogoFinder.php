<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Finder;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

readonly class LogoFinder
{
    /**
     * Define specific logo who was used
     */
    public function findLogo(Adherent $adherent): null|string|Media
    {
        if ($adherent->getLogo()) {
            return $adherent->getLogo();
        }

        $officialLogo = SettingManager::get('setting.pdf.logo.name');
        if ($officialLogo) {
            return $officialLogo;
        }

        return null;
    }
}
