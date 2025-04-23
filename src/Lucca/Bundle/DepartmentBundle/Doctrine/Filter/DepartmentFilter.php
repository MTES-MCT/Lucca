<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/*
 * This filter is used to filter entities based on the department they belong to.
 * It is applied to all entities that have a "department" field.
 */
class DepartmentFilter extends SQLFilter
{
    private ?int $departmentId;

    public function setDepartmentId(int $departmentId): void
    {
        $this->departmentId = $departmentId;
    }

    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        $associations = array_keys($targetEntity->getAssociationMappings());
        if (!in_array('department', $associations) || !$this->departmentId) {
            return '';
        }

        // Ici on considère que votre entité possède un champ "subdomain".
        // Il est important d’échapper correctement la valeur.
        $quotedDepartmentId = $this->getConnection()->quote($this->departmentId);

        return sprintf('%s.department_id = %s', $targetTableAlias, $quotedDepartmentId);
    }
}
