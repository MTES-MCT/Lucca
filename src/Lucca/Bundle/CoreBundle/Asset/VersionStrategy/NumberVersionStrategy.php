<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Asset\VersionStrategy;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

readonly class NumberVersionStrategy implements VersionStrategyInterface
{
    /**
     * eg : /bundles/luccathemeinspinia/css/bootstrap.min.css?version?forceRefresh
     */
    public function __construct(
        private string $version,
        private string $forceRefresh,
    )
    {
    }

    /**
     * Use parameters used in the main configuration in Symfony file
     * to build suffix on asset
     */
    public function getVersion($path): string
    {
        $suffix = '';

        if ($this->version) {
            $suffix = '?v' . $this->version;
        }

        if ($this->forceRefresh) {
            $suffix .= '?f' . $this->forceRefresh;
        }

        return $suffix;
    }

    /**
     * Apply this configuration on assets sent to client browser
     */
    public function applyVersion($path): string
    {
        $suffix = $this->getVersion($path);

        /** if suffix is empty, just return the path */
        if ('' === $suffix) {
            return $path;
        }

        /** return path of file concat with a suffix generated in this file */
        return $path . $suffix;
    }
}
