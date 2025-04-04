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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/')]
#[IsGranted('ROLE_ADMIN')]
class ParameterController extends AbstractController
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    )
    {
    }

    /**
     * Show main dashboard
     */
    #[Route(path: '/parametres', name: 'lucca_core_parameter', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function homeAction(): Response
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return $this->render('@LuccaCore/Parameter/home.html.twig', [
            'user' => $user,
        ]);
    }
}
