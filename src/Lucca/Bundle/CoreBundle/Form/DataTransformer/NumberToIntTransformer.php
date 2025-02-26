<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class NumberToIntTransformer implements DataTransformerInterface
{
    /**
     * Transforms int to a float.
     */
    public function transform(mixed $value): ?float
    {
        if ($value) {
            $result = $value / 100;

            return (double)$result;
        }

        return null;
    }

    /**
     * Transforms a float to an int.
     */
    public function reverseTransform(mixed $value): ?int
    {
        if ($value) {
            $result = $value * 100;

            return (int)$result;
        }

        return null;
    }
}
