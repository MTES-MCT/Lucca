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

trait ArrayLikeFilterTrait
{
    /**
     * Apply a LIKE filter on an array of values for a given field.
     *
     * @param QueryBuilder $qb
     * @param string $field
     * @param array $values
     * @return QueryBuilder
     */
    private function addArrayLikeFilter(QueryBuilder $qb, string $field, array $values): QueryBuilder
    {
        if (empty($values)) {
            return $qb;
        }

        $orX = $qb->expr()->orX();

        foreach ($values as $i => $value) {
            $paramName = str_replace('.', '_', $field) . $i; // safe param name
            $orX->add($qb->expr()->like("LOWER($field)", ":$paramName"));
            $qb->setParameter($paramName, '%' . mb_strtolower($value) . '%');
        }

        $qb->andWhere($orX);

        return $qb;
    }
}
