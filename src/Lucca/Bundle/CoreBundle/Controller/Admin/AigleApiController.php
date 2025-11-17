<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\SecurityBundle\Form\BrowserLoginAttemptType;

#[Route(path: '/admin/aigle-api')]
#[IsGranted('ROLE_ADMIN')]
class AigleApiController extends AbstractController
{

    #[Route(path: '/', name: 'lucca_core_admin_aigle_api_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $form = $this->createForm(BrowserLoginAttemptType::class);

        return $this->render('@LuccaCore/Admin/aigleApi.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
