<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Controller;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\ContentBundle\Entity\Page;

#[Route(path: '/page')]
class PageController extends AbstractController
{
    /**
     * Show Page page
     */
    #[Route(path: '/{slug}', name: 'lucca_content_page', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function pageAction(
        #[MapEntity(mapping: ['slug' => 'slug'])] Page $page,
    ): Response
    {
        return $this->render('@LuccaContent/Page/show-public.html.twig', [
            'page' => $page,
        ]);
    }
}
