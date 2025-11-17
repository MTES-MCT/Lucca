<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RestService
{
    /**
     * Prepares and validates filters for REST endpoints (listing queries).
     *
     * @param array $requestFilters     Filters received from Request->query->all()
     * @param array $allowedWithDefault Array of allowed filters with optional default values.
     *                                  Example:
     *                                  [
     *                                      'INSEE',
     *                                      'townName',
     *                                      'plotCode',
     *                                      'number',
     *                                      'page' => 1,
     *                                      'limit' => 30,
     *                                  ]
     *
     * @return array $filters Cleaned, validated, and ready to be used by the Repository.
     *
     * @throws BadRequestException If a filter is provided that is not allowed.
     */
    public function prepareFilters(array $requestFilters, array $allowedWithDefault): array
    {
        $filters = $requestFilters;

        // Build allowed keys list and defaults
        $allowedKeys = [];
        $defaults = [];
        foreach ($allowedWithDefault as $key => $value) {
            if (is_int($key)) {
                $allowedKeys[] = $value;
            } else {
                $allowedKeys[] = $key;
                $defaults[$key] = $value;
            }
        }

        // Check for disallowed filters
        foreach ($filters as $key => $value) {
            if (!in_array($key, $allowedKeys, true)) {
                throw new BadRequestException(sprintf('Filter not allowed: %s', $key));
            }
        }

        // Add defaults for missing filters
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $filters)) {
                $filters[$key] = $value;
            }
        }

        // Ensure all filters (except page/limit) are arrays
        foreach ($filters as $key => $value) {
            if (in_array($key, ['page', 'limit'], true)) {
                continue;
            }

            $filters[$key] = is_array($value) ? $value : [$value];
        }

        return $filters;
    }
}
