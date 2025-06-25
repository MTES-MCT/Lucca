<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\ContentBundle\Entity\Area;

#[Route(path: '/')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserDepartmentResolver $userDepartmentResolver,
    )
    {
    }

    /**
     * Show main dashboard
     */
    #[Route(path: '/', name: 'lucca_content_dashboard', methods: ['GET'])]
    public function homeAction(): Response
    {
        $area = $this->em->getRepository(Area::class)->findDashboard(Area::POSI_CONTENT);

        if ($this->userDepartmentResolver->getCode() === 'admin') {
            // If the user is in the admin department, redirect to the admin dashboard
            return $this->redirectToRoute('lucca_user_security_login', [
                'dep_code' => 'admin',
            ]);
        }

        return $this->render('@LuccaContent/Dashboard/home.html.twig', [
            'area' => $area,
        ]);
    }
}
