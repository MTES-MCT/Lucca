<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Session;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class CustomPdoSessionHandler extends PdoSessionHandler
{
    private bool $disableTimestampUpdate = false;
    private RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack,
        #[\SensitiveParameter] \PDO|string|null $pdoOrDsn = null,
        #[\SensitiveParameter] array $options = [],
    )
    {
        parent::__construct($pdoOrDsn, $options);

        $this->requestStack = $requestStack;
        $pathExploded = explode('/', $this->requestStack->getCurrentRequest()?->getRequestUri() ?? '');

        // On cible ici les routes API (par exemple celles commençant par "/api")
        if (in_array('api', $pathExploded, true)) {
            $this->disableTimestampUpdate = true;
        }
    }

    protected function doWrite(#[\SensitiveParameter] string $sessionId, string $data): bool
    {
        // Si le flag est activé, on n’effectue pas la mise à jour de l’horodatage
        if ($this->disableTimestampUpdate) {
            return true;
        }

        return parent::doWrite($sessionId, $data);
    }
}
