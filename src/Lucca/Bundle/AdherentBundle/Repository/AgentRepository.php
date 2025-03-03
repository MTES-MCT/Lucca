<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Repository;

use Doctrine\ORM\QueryBuilder;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;

class AgentRepository extends LuccaRepository
{
    private function queryByAdherent(Adherent $adherent): QueryBuilder
    {
        $qb = $this->createQueryBuilder('agent')
            ->leftJoin('agent.adherent', 'adherent')->addSelect('adherent')
            ->leftJoin('adherent.town', 'town')->addSelect('town')
            ->leftJoin('town.intercommunal', 'town_intercommunal')->addSelect('town_intercommunal')
            ->leftJoin('adherent.intercommunal', 'intercommunal')->addSelect('intercommunal')
            ->leftJoin('adherent.service', 'service')->addSelect('service')
            ->leftJoin('agent.tribunal', 'tribunal')->addSelect('tribunal');

        /** Get all agent of this Intercommunal + all agents of Intercommunal town's */
        if ($adherent->getIntercommunal()) {
            $qb->andWhere($qb->expr()->eq('intercommunal', ':q_intercommunal'))
                ->orWhere($qb->expr()->eq('town_intercommunal', ':q_intercommunal'))
                ->setParameter('q_intercommunal', $adherent->getIntercommunal());
        }
        elseif ($adherent->getTown()) {
            $qb->andWhere($qb->expr()->eq('adherent', ':q_adherent'))
                ->setParameter('q_adherent', $adherent);
        }

        return $qb;
    }

    /**
     * Find all agents by Adherent Entity
     */
    public function findAllByAdherent(Adherent $adherent): array
    {
        $qb = $this->queryByAdherent($adherent);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get all agents by Adherent Entity
     */
    public function getAllByAdherent(Adherent $adherent): QueryBuilder
    {
        return $this->queryByAdherent($adherent);
    }

    /**
     * Get all agents by Adherent Entity
     */
    public function getAllActiveByAdherent(Adherent $adherent): QueryBuilder
    {
        $qb = $this->queryByAdherent($adherent);

        $qb->andWhere($qb->expr()->eq('agent.enabled', ':q_enabled'))
            ->setParameter(':q_enabled', true);

        return $qb;
    }
}
