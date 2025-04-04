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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Lucca\Bundle\ContentBundle\Entity\Area;

class RenderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * Show main dashboard
     */
    public function footerAction(): Response
    {
        $area = $this->em->getRepository(Area::class)->findDashboard(Area::POSI_FOOTER);

        return $this->render('@LuccaContent/Render/footer.html.twig', [
            'footer' => $area,
        ]);
    }
}
