<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\UserBundle\Entity\User;

class AdherentExtension extends AbstractExtension
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * Get twig filters
     * Add is_safe param to render HTML in filter
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('isCountryAgent', [$this, 'isCountryAgent']),
        ];
    }

    /**
     * Check if the current user is a country agent or not
     */
    public function isCountryAgent(User $user): bool
    {
        $adherent = $this->em->getRepository(Adherent::class)->findOneByUser($user);

        if ($adherent->getFunction() == Adherent::FUNCTION_COUNTRY_AGENT){
            return true;
        }

        return false;
    }

    public function getName(): string
    {
        return 'lucca.twig.adherent';
    }
}
