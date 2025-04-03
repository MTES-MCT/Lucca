<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SpecialController extends AbstractController
{
    /**
     * Show Privacy Policy
     */
    #[Route(path: '/politique-confidentialite', name: 'lucca_content_privacy_policy', methods: ['GET'])]
    public function privacyPolicyAction(): Response
    {
        return $this->render('@LuccaContent/Special/privacyPolicy.html.twig');
    }

    /**
     * Show Terms of Service
     */
    #[Route(path: '/mentions-legales', name: 'lucca_content_terms_service', methods: ['GET'])]
    public function termsServiceAction(): Response
    {
        return $this->render('@LuccaContent/Special/termsService.html.twig');
    }
}
