<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Controller\Anonymous;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route(path: '/')]
class HomeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenStorageInterface $tokenStorage,
        private RequestStack           $requestStack,
    )
    {
    }
    /**
     * Show the home page
     */
    #[Route(path: '/', name: 'lucca_core_home', methods: ['GET'])]
    public function homeAction(): Response
    {

        $departments = $this->entityManager->getRepository(Department::class)->findForHomeList();

        //unlogged users to avoid any problems
        $this->tokenStorage->setToken(null);
        $this->requestStack->getSession()->invalidate();

        return $this->render('@LuccaCore/Home/HomePage.html.twig',
            [
                'departments' => $departments,
            ]
        );
    }
}
