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
use Symfony\Component\HttpFoundation\RequestStack;

class DepartmentFilter extends SQLFilter
{
    // Stockage du RequestStack
    private $requestStack;

    // Méthode d’injection du RequestStack (appelée depuis le container)
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Cette méthode est appelée pour ajouter une contrainte SQL sur la requête.
     *
     * @param ClassMetadata $targetEntity
     * @param string $targetTableAlias
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        // Appliquer le filtre uniquement sur l'entité concernée
        if ($targetEntity->getReflectionClass()->getName() !== 'App\Entity\MyEntity') {
            return '';
        }

        // Récupérer le sous-domaine depuis la requête HTTP via le RequestStack
        $currentRequest = $this->requestStack->getCurrentRequest();
        $subdomain = null;
        if ($currentRequest) {
            // Exemple : si le host est "blog.example.com", on considère "blog" comme sous-domaine
            $hostParts = explode('.', $currentRequest->getHost());
            if (count($hostParts) > 2) {
                $subdomain = $hostParts[0];
            }
        }

        // En cas d'absence de sous-domaine, utiliser une valeur par défaut
        if (null === $subdomain) {
            $subdomain = 'demo';
        }

        // Ici on considère que votre entité possède un champ "subdomain".
        // Il est important d’échapper correctement la valeur.
        $quotedSubdomain = $this->getConnection()->quote($subdomain);

        return sprintf('%s.subdomain = %s', $targetTableAlias, $quotedSubdomain);
    }
}
