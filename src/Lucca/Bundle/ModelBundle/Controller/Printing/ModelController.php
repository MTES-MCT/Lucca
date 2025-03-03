<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Controller\Printing;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\ModelBundle\Printer\PagePrinter;
use Lucca\Bundle\ModelBundle\Entity\Model;

#[Route(path: '/model')]
#[IsGranted('ROLE_LUCCA')]
class ModelController extends AbstractController
{
    public function __construct(
        private readonly AdherentFinder $adherentFinder,
        private readonly PagePrinter $pagePrinter,
        private readonly Pdf $pdf,
    )
    {
    }

    /**
     * Visualize a page
     */
    #[Route(path: '-{id}/print', name: 'lucca_model_print', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function modelPagePrintAction(Model $model): Response
    {
        $filename = 'Visualisation du modèle';

        /** Who is connected */
        $adherent = $this->adherentFinder->whoAmI();

        $html = $this->renderView('@LuccaModel/Printing/example.html.twig', array('model' => $model, 'adherent' => $adherent));

        $options = $this->pagePrinter->createModelOption($model, [], $adherent);

        $generatedPdf = $this->pdf->getOutputFromHtml($html, $options);

        return new Response(
            $generatedPdf, 200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'
            ]
        );
    }
}
