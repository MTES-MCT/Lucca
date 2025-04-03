<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Printing;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Knp\Snappy\Pdf;
use Lucca\Bundle\FolderBundle\Printer\MayorLetterPrinter;
use Lucca\Bundle\MinuteBundle\Entity\Updating;
use Lucca\Bundle\FolderBundle\Utils\MayorLetterManager;
use Lucca\Bundle\ModelBundle\Printer\PagePrinter;
use Lucca\Bundle\ModelBundle\Service\ModelFinder;
use Lucca\Bundle\MinuteBundle\Printer\ControlPrinter;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PdfReaderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tomsgu\PdfMerger\Exception\{FileNotFoundException, InvalidArgumentException};
use Tomsgu\PdfMerger\{PdfCollection, PdfFile, PdfMerger};

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\FolderBundle\Entity\MayorLetter;
use Lucca\Bundle\ModelBundle\Entity\Model;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

#[Route(path: '/mayor-letter')]
#[IsGranted('ROLE_LUCCA')]
class MayorLetterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ControlPrinter         $controlPrinter,
        private readonly MayorLetterManager     $mayorLetterManager,
        private readonly MayorLetterPrinter     $mayorLetterPrinter,
        private readonly ModelFinder            $modelFinder,
        private readonly PagePrinter            $pagePrinter,
        private readonly Pdf                    $pdf,
    )
    {
    }

    /**
     * Judicial Print letter
     *
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     * @throws PdfReaderException
     */
    #[Route(path: '-{id}/print', name: 'lucca_mayor_letter_print', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function mayorLetterPrintAction(MayorLetter $mayorLetter): Response
    {
        /** Step 1 : init */
        $filename = 'Courrier de rattachement - ' . $mayorLetter->getTown()->getName() . ' ' . $mayorLetter->getTown()->getCode();

        if ($mayorLetter->getDateSended()) {
            $filename = $filename . ' - ' . $mayorLetter->getDateSended()->format('Y-m-d');
        }

        /** Step 2 : Search logo of the adherent */
        $adherent = $mayorLetter->getAdherent();

        $logo = null;
        /** Define Logo and set margin */
        if (!SettingManager::get('setting.pdf.logoInHeader.name')) {
            $logo = $this->controlPrinter->defineLogo($adherent);
        }

        /** Step 3 : Create html */
        $html = $this->renderView('@LuccaFolder/MayorLetter/Printing/Basic/mayorLetter_print.html.twig', array(
            'mayorLetter' => $mayorLetter, 'adherent' => $adherent, 'officialLogo' => $logo
        ));

        $options = $this->mayorLetterPrinter->createMayorLetterOptions($adherent);

        $generatedPdf = $this->pdf->getOutputFromHtml($html, $options);

        $this->mayorLetterManager->deleteMayorLetter($mayorLetter);

        return new Response(
            $this->generatePDFFolders($mayorLetter, $generatedPdf), 200,
            array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"')
        );
    }

    /**
     * Function used to generate PV in mayor letter
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     * @throws PdfReaderException
     */
    private function generatePDFFolders(MayorLetter $mayorLetter, $generatedPDFLetter): RedirectResponse|string
    {
        /** Init var with tools need to merge pdf */
        $pdf = new PdfCollection();
        $filesystem = new Filesystem();
        $fpdi = new Fpdi();
        $merger = new PdfMerger($fpdi);

        /** Init empty array in order to delete temp files when finish */
        $filesNames = [];

        /** Store filename that will be used for the final pdf */
        $filename = sprintf('Lettre au maire - %s', $mayorLetter->getId());

        /** Path where the temp pdf are stored */
        $path = $this->getParameter('lucca_media.upload_tmp_directory') . 'pdfToPrint/';

        /** If the temp directory doesn't exist create it */
        if (!$filesystem->exists($path)) {
            $filesystem->mkdir($path);
        }

        /************* Add Mayor letter the final var *****************/
        /** Store file path to create temp file and be able to delete it later */
        $filePath = $path . $filename . '.pdf';
        $filesNames[] = $filePath;

        /** Store file in temp folder */
        $filesystem->appendToFile($filePath, $generatedPDFLetter);
        $pdf->addPDF($filePath, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);

        foreach ($mayorLetter->getFolders() as $folder) {
            /** Step 1 : Find updating */
            $update = $this->em->getRepository(Updating::class)->findUpdatingByControl($folder->getControl());

            /** Step 2 : Get adherent and model corresponding*/
            $minute = $folder->getMinute();
            $adherent = $minute->getAdherent();

            /** Try to find the model corresponding to the document */
            $model = $this->modelFinder->findModel(Model::DOCUMENTS_FOLDER, $adherent);

            /** If model is null it's mean there is no model created, so we can't generate the PDF */
            if ($model === null) {
                $this->addFlash('danger', 'flash.model.notFound');

                return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
            }

            /** Step 3 : Create html */
            $html = $this->renderView('@LuccaFolder/Folder/Printing/Basic/doc_print.html.twig', [
                'model' => $model, 'minute' => $minute, 'adherent' => $minute->getAdherent(), 'folder' => $folder, 'update' => $update
            ]);

            $options = $this->pagePrinter->createModelOption($model, [], $adherent);

            $generatedPdf = $this->pdf->getOutputFromHtml($html, $options);

            /** Store file path to create temp file and be able to delete it later */
            $filePath = $path . 'pv-' . $folder->getId() . '.pdf';
            $filesNames[] = $filePath;

            /** Store file in temp folder */
            $filesystem->appendToFile($filePath, $generatedPdf);

            /** Add pdf to the final var */
            $pdf->addPDF($filePath, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);
        }

        /** Merge all the pdf into a final one, get a string in order to be able to return it to the main function */
        $merged = $merger->merge($pdf, $filename, PdfMerger::MODE_STRING);

        /** Then delete the temp files */
        foreach ($filesNames as $name) {
            $filesystem->remove($name);
        }

        return $merged;
    }
}
