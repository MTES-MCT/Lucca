<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Lucca\Bundle\MediaBundle\Entity\{MediaAsyncInterface, MediaListAsyncInterface};

class ClassEntityExtension extends AbstractExtension
{
    /**
     * Get twig filters
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('classEntity', [$this, 'getClassEntity']),
            new TwigFilter('canAsyncRemove', [$this, 'isImplementsAsync']),
        ];
    }

    /**
     * Get class extension
     *
     * @param [type] $entity
     */
    public function getClassEntity($entity): string
    {
        if ($entity === null) {
            return '';
        }

        $nameEntity = (new \ReflectionClass($entity))->getName();

        return str_replace('Proxies\__CG__\\', '', $nameEntity);
    }

    /**
     * Is the entity implements Async Interfaces
     *
     * @param [type] $p_entity
     */
    public function isImplementsAsync($entity): string|bool
    {
        if ($entity === null) {
            return '';
        }

        $classEntity = new \ReflectionClass($entity);

        return $classEntity->implementsInterface(MediaAsyncInterface::class)
        || $classEntity->implementsInterface(MediaListAsyncInterface::class);
    }

    public function getName(): string
    {
        return 'lucca.twig.classEntity';
    }
}
