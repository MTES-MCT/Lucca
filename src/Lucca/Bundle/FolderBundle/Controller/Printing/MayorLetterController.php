<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Printing;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\MayorLetter;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Lucca\AdherentBundle\Entity\Adherent;
use Lucca\ModelBundle\Entity\Model;
use Lucca\SettingBundle\Utils\SettingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PdfReaderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tomsgu\PdfMerger\Exception\FileNotFoundException;
use Tomsgu\PdfMerger\Exception\InvalidArgumentException;
use Tomsgu\PdfMerger\PdfCollection;
use Tomsgu\PdfMerger\PdfFile;
use Tomsgu\PdfMerger\PdfMerger;

/**
 * Class MayorLetterController
 *
 * @Route("/mayor-letter")
 * @Security("has_role('ROLE_LUCCA')")
 *
 * @package Lucca\MinuteBundle\Controller\Printing
 * @author Lisa <lisa.alvarez@numeric-wave.eu>
 */
class MayorLetterController extends Controller
{
    /**
     * Judicial Print letter
     *
     * @Route("-{id}/print", name="lucca_mayor_letter_print", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * /**
     * @param MayorLetter $p_mayorLetter
     * @param Request $p_request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function mayorLetterPrintAction(MayorLetter $p_mayorLetter, Request $p_request)
    {
        /** Step 1 : init snappy */
        $snappy = $this->get('knp_snappy.pdf');
        $filename = 'Courrier de rattachement - ' . $p_mayorLetter->getTown()->getName() . ' ' . $p_mayorLetter->getTown()->getCode();

        if ($p_mayorLetter->getDateSended())
            $filename = $filename . ' - ' . $p_mayorLetter->getDateSended()->format('Y-m-d');

        /** Step 2 : Search logo of the adherent */
        /** @var Adherent $adherent */
        $adherent = $p_mayorLetter->getAdherent();

        $logo = null;
        /** Define Logo and set margin */
        if (!SettingManager::get('setting.pdf.logoInHeader.name')) {
            $logo = $this->get('lucca.utils.printer.control')->defineLogo($adherent);
        }

        /** Step 3 : Create html */
        $html = $this->renderView('LuccaMinuteBundle:MayorLetter/Printing/Basic:mayorLetter_print.html.twig', array(
            'mayorLetter' => $p_mayorLetter, 'adherent' => $adherent, 'officialLogo' => $logo
        ));

        $options = $this->get('lucca.utils.printer.mayor.letter')->createMayorLetterOptions($adherent);

        $generatedPdf = $snappy->getOutputFromHtml($html, $options);

        $this->get('lucca.manager.mayor.letter')->deleteMayorLetter($p_mayorLetter);

        return new Response(
            $this->generatePDFFolders($p_mayorLetter, $generatedPdf), 200,
            array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"')
        );
    }

    /**
     * Function used to generate PV in mayor letter
     *
     * @param MayorLetter $p_mayorLetter
     * @param $p_generatedPDFLetter
     * @return string|RedirectResponse
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     * @throws PdfReaderException
     */
    private function generatePDFFolders(MayorLetter $p_mayorLetter, $p_generatedPDFLetter)
    {
        /** Init var with tools need to merge pdf */
        $pdf = new PdfCollection();
        $filesystem = new Filesystem();
        $fpdi = new Fpdi();
        $merger = new PdfMerger($fpdi);
        $snappy = $this->get('knp_snappy.pdf');

        /** Init empty array in order to delete temp files when finish */
        $filesNames = array();

        /** Store filename that will be used for the final pdf */
        $filename = sprintf('Lettre au maire - %s', $p_mayorLetter->getId());

        /** Path where the temp pdf are stored */
        $path = $this->getParameter('env(LUCCA_UPLOAD_TEMP_DIR)') . 'pdfToPrint/';

        /** If the temp directory doesn't exist create it */
        if (!$filesystem->exists($path))
            $filesystem->mkdir($path);

        /************* Add Mayor letter the final var *****************/
        /** Store file path to create temp file and be able to delete it later */
        $filePath = $path . $filename . '.pdf';
        $filesNames[] = $filePath;

        /** Store file in temp folder */
        $filesystem->appendToFile($filePath, $p_generatedPDFLetter);
        $pdf->addPDF($filePath, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);

        foreach ($p_mayorLetter->getFolders() as $folder) {
            $em = $this->getDoctrine()->getManager();

            /** Step 1 : Find updating */
            $update = $em->getRepository('LuccaMinuteBundle:Updating')->findUpdatingByControl($folder->getControl());

            /** Step 2 : Get adherent and model corresponding*/
            $minute = $folder->getMinute();
            $adherent = $minute->getAdherent();

            /** Try to find the model corresponding to the document */
            $model = $this->get('lucca.finder.model')->findModel(Model::DOCUMENTS_FOLDER, $adherent);

            /** If model is null it's mean there is no model created, so we can't generate the PDF */
            if ($model === null) {
                $this->addFlash('danger', 'flash.model.notFound');
                return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
            }

            /** Step 3 : Create html */
            $html = $this->renderView('@LuccaMinute/Folder/Printing/Basic/doc_print.html.twig', [
                'model' => $model, 'minute' => $minute, 'adherent' => $minute->getAdherent(), 'folder' => $folder, 'update' => $update
            ]);

            $options = $this->get('lucca.utils.printer.model.page')->createModelOption($model, array(), $adherent);

            $generatedPdf = $snappy->getOutputFromHtml($html, $options);

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