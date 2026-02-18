<?php

namespace Lucca\Bundle\CoreBundle\Repository\Partial;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait DataTableTrait
{
    public function findForDatatable(QueryBuilder $qb, array $params): array
    {
        $rootAlias = $qb->getRootAliases()[0];

        // 1. Total Count (Before any search filters)
        $countQb = clone $qb;
        $countQb->select("COUNT(DISTINCT $rootAlias.id)");
        $recordsTotal = (int) $countQb->getQuery()->getSingleScalarResult();

        // 2. GLOBAL Search Logic
        $searchValue = $params['search']['value'] ?? null;
        if (!empty($searchValue)) {
            $orX = $qb->expr()->orX();
            foreach ($params['columns'] ?? [] as $i => $col) {
                if ($col['searchable'] === 'true' && !empty($col['data']) && $col['data'] !== 'actions') {
                    $fieldName = strpos($col['data'], '.') === false ? "$rootAlias.{$col['data']}" : $col['data'];
                    $orX->add($qb->expr()->like($fieldName, ":global_search_$i"));
                    $qb->setParameter("global_search_$i", "%$searchValue%");
                }
            }
            if ($orX->count() > 0) $qb->andWhere($orX);
        }

        // 3. INDIVIDUAL Column Search Logic (Added part)
        foreach ($params['columns'] ?? [] as $i => $col) {
            $colSearchValue = $col['search']['value'] ?? null;

            // If an individual column search is provided
            if (!empty($colSearchValue) && $col['searchable'] === 'true' && !empty($col['data'])) {
                $fieldName = strpos($col['data'], '.') === false ? "$rootAlias.{$col['data']}" : $col['data'];

                // We use andWhere here because column filters are cumulative
                $qb->andWhere($qb->expr()->like($fieldName, ":col_search_$i"));
                $qb->setParameter("col_search_$i", "%$colSearchValue%");
            }
        }

        // 4. Dynamic Ordering
        if (isset($params['order'][0])) {
            $colIndex = $params['order'][0]['column'];
            $colName = $params['columns'][$colIndex]['data'] ?? null;
            $dir = (isset($params['order'][0]['dir']) && strtolower($params['order'][0]['dir']) === 'desc') ? 'DESC' : 'ASC';

            if ($colName && $colName !== 'actions') {
                $fieldName = strpos($colName, '.') === false ? "$rootAlias.$colName" : $colName;
                $qb->orderBy($fieldName, $dir);
            }
        }

        // 5. Pagination
        $start = (int)($params['start'] ?? 0);
        $length = (int)($params['length'] ?? 10);
        $qb->setFirstResult($start)->setMaxResults($length);

        // 6. Execution
        $paginator = new Paginator($qb->getQuery(), true);

        return [
            'draw'            => (int)($params['draw'] ?? 1),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => count($paginator),
            'data'            => $qb->getQuery()->getArrayResult(),
        ];
    }
}
