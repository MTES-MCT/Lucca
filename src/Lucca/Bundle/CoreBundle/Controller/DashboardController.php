<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;

#[Route(path: '/')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly AdherentFinder $adherentFinder
    )
    {
    }

    /**
     * Main dashboard
     */
    #[Route(path: '/dashboard', name: 'lucca_core_dashboard', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function homeAction(): Response
    {
        /** Who is connected ;) */
        $adherent = $this->adherentFinder->whoAmI();

        /** Only render the view because all treatment is done with js and api */
        return $this->render('@LuccaCore/Dashboard/home.html.twig', [
            'adherentId' => $adherent->getId(),
            'adherent' => $adherent
        ]);
    }

    /**
     * Map of folders + minute + control
     */
    #[Route(path: '/map', name: 'lucca_core_map', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function mapAction(): Response
    {
        /** Only render the view because all treatment is done with js and api */
        return $this->render('@LuccaCore/Dashboard/map.html.twig', [
            'adherentId' => null,
        ]);
    }
}
