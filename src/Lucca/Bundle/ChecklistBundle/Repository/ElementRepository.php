<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ChecklistBundle\Repository;

use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;
use Lucca\Bundle\ChecklistBundle\Entity\Checklist;

class ElementRepository extends LuccaRepository
{
    /**
     * Find all active elements by Checklist
     * use on FolderController
     */
    public function findActiveByChecklist(Checklist $checklist): array
    {
        $qb = $this->createQueryBuilder('element')
            ->leftJoin('element.checklist', 'checklist')
            ->addSelect('checklist');

        $qb->where($qb->expr()->eq('element.enabled', ':q_enabled'))
            ->andWhere($qb->expr()->eq('checklist', ':q_checklist'))
            ->setParameter(':q_enabled', true)
            ->setParameter(':q_checklist', $checklist);

        $qb->orderBy('element.position', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
