<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\LogBundle\Controller\Admin;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\LogBundle\Form\LogFilterType;

use Lucca\Bundle\LogBundle\Entity\Log;

#[Route(path: '/log')]
#[IsGranted('ROLE_ADMIN')]
class LogController extends AbstractController
{
    /**
     * List of Log
     *
     * @throws Exception
     */
    #[Route(path: '/', name: 'lucca_log_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $form = $this->createForm(LogFilterType::class);

        return $this->render('@LuccaLog/Log/Admin/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Log entity.
     */
    #[Route(path: '/{id}', name: 'lucca_log_show', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Log $log): Response
    {
        return $this->render('@LuccaLog/Log/Admin/show.html.twig', [
            'log' => $log,
        ]);
    }
}
