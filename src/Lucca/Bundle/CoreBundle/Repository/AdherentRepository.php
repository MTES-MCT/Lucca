<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;

trait AdherentRepository
{
    /**
     * Get adherent values
     */
    public function getValuesAdherent($adherent, $qb): QueryBuilder
    {
        if ($adherent->getIntercommunal()) {
            $qb->andWhere($qb->expr()->eq('plot_intercommunal', ':q_intercommunal'))
                ->setParameter('q_intercommunal', $adherent->getIntercommunal());
        } elseif ($adherent->getTown()) {
            $qb->andWhere($qb->expr()->eq('plot_town', ':q_town'))
                ->setParameter(':q_town', $adherent->getTown());
        }

        return $qb;
    }
}
