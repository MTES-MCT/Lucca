<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Printing;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\Error;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\{Minute, Updating};
use Lucca\Bundle\ModelBundle\Entity\Model;
use Lucca\Bundle\ModelBundle\Printer\PagePrinter;
use Lucca\Bundle\ModelBundle\Service\ModelFinder;

#[Route(path: '/minute-{minute_id}/folder-')]
#[IsGranted('ROLE_LUCCA')]
class FolderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ModelFinder           $modelFinder,
        private readonly PagePrinter           $pagePrinter,
        private readonly Pdf                   $pdf,
    )
    {
    }

    /**
     * Print a Folder
     */
    #[Route(path: '{id}/document/print', name: 'lucca_folder_doc_print', methods: ['GET'])]
    #[Route(path: '{id}/document/preprint', name: 'lucca_folder_doc_preprint', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function folderDocPrintAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Folder                               $folder,
        Request                              $request
    ): Response {
        /** Check in route if it's preprint route that is called */
        $isPrePrint = str_contains($request->attributes->get('_route'), "preprint");

        /** Step 1 */
        $filename = sprintf('Folder %s', $folder->getNum());

        $update = $this->em->getRepository(Updating::class)->findUpdatingByControl($folder->getControl());

        /** Step 2 : Get adherent and model corresponding*/
        $adherent = $minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->modelFinder->findModel(Model::DOCUMENTS_FOLDER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        /** Step 3 : Create html */
        try {
            $html = $this->renderView('@LuccaFolder/Folder/Printing/Basic/doc_print.html.twig', [
                'model' => $model, 'minute' => $minute, 'adherent' => $minute->getAdherent(), 'folder' => $folder, 'update' => $update, 'isPreprint' => $isPrePrint
            ]);
        } catch (Error $twig_Error) {
            $html = null;
            echo 'Twig_Error has been thrown - Body ' . $twig_Error->getMessage();
        }

        $var = [
            "date" => (new DateTime("now"))->format("d/m/Y"),
            "structureOffice" => $adherent->getStructureOffice(),
            "adherentName" => $adherent->getOfficialName(),
            "adherentMail" => $adherent->getEmailPublic(),
            "adherentPhone" => $adherent->getOfficialPhone(),
            "agentName" => $folder->getMinute()->getAgent()->getOfficialName()
        ];

        $options = $this->pagePrinter->createModelOption($model, $var, $adherent);

        try {
            $generatedPdf = $this->pdf->getOutputFromHtml($html, $options);
        } catch (Exception) {
            $this->addFlash('danger', 'flash.folder.cannotPrint');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return new Response(
            $generatedPdf, 200,
            ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"']
        );
    }
}
