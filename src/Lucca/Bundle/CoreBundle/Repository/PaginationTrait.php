<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;

trait PaginationTrait
{
    /**
     * Execute a paginated query and return results + pagination metadata.
     *
     * @param QueryBuilder $qb Base QueryBuilder (already filtered)
     * @param int $page
     * @param int $limit
     * @param string $countField
     * @return array ['data' => [...], 'meta' => [...]]
     */
    public function paginate(QueryBuilder $qb, int $page, int $limit = 30, string $countField = 'id'): array
    {
        $offset = ($page - 1) * $limit;

        $alias = $qb->getRootAliases()[0] ?? null;

        if (!$alias) {
            throw new \InvalidArgumentException('Cannot determine root alias from QueryBuilder.');
        }

        $countQb = clone $qb;
        $countQb->select("COUNT(DISTINCT $alias.$countField)");
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        // Ensure groupBy primary key for DISTINCT results
        if (empty($qb->getDQLPart('groupBy'))) {
            $qb->addGroupBy("$alias.$countField");
        }

        $data = $qb->getQuery()->getArrayResult();

        return [
            'data' => $data,
            'meta' => [
                'page'        => $page,
                'limit'       => $limit,
                'total'       => $total,
                'hasNextPage' => ($page * $limit) < $total,
            ],
        ];
    }
}