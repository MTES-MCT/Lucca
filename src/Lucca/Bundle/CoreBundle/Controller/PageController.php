<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Controller;

use Doctrine\ORM\{EntityManagerInterface, NonUniqueResultException};
use Knp\Snappy\Pdf;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\ContentBundle\Entity\{Area, Page};

#[Route(path: '/')]
#[IsGranted('ROLE_USER')]
class PageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly Pdf $pdf,
    )
    {
    }

    /**
     * Show main tools page
     *
     * @throws NonUniqueResultException
     */
    #[Route(path: '/tools', name: 'lucca_core_page_tools', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function toolsAction(): ?Response
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $area = $this->em->getRepository('LuccaContentBundle:Area')->findDashboard(Area::POSI_ADMIN);

        return $this->render('@LuccaCore/Page/tools.html.twig', [
            $user => 'user',
            $area => 'area',
        ]);
    }

    /**
     * Show Page page
     */
    #[Route(path: '/tools/{slug}', name: 'lucca_core_page_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function pageAction(
        #[MapEntity(mapping: ['slug' => 'slug'])] Page $page,
    ): Response
    {
        return $this->render('@LuccaCore/Page/show.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * Print a page
     */
    #[Route(path: '/tools/{slug}/print', name: 'lucca_core_page_show_print', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function pagePrintAction(
        #[MapEntity(mapping: ['slug' => 'slug'])] Page $page,
    ): Response
    {
        $html = $this->renderView('@LuccaContent/Page/print.html.twig', [
            'page' => $page,
        ]);

        $filename = sprintf('Fiche outils - %s', $page->getName());

        return new Response(
            $this->pdf->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'
                //'Content-Disposition' => 'attachment; filename="'.$filename.'.pdf"' sprintf('attachment; filename="%s"', $filename)
            ]
        );
    }
}
