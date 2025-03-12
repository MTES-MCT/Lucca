<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\Printing;

use Knp\Snappy\Pdf;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PdfReaderException;
use Tomsgu\PdfMerger\Exception\FileNotFoundException;
use Tomsgu\PdfMerger\Exception\InvalidArgumentException;
use Tomsgu\PdfMerger\PdfCollection;
use Tomsgu\PdfMerger\PdfFile;
use Tomsgu\PdfMerger\PdfMerger;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\ModelBundle\Entity\Model;
use Lucca\Bundle\ModelBundle\Printer\PagePrinter;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\ControlEdition;
use Lucca\Bundle\MinuteBundle\Entity\Human;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\ModelBundle\Service\ModelFinder;

#[Route('/minute-{minute_id}/control-')]
#[IsGranted('ROLE_LUCCA')]
class ControlController extends AbstractController
{
    /** Setting if use agent of refresh or minute agent */
    private mixed $useRefreshAgentForRefreshSignature;

    public function __construct(
        private readonly PagePrinter         $pagePrinter,
        private readonly TranslatorInterface $translator,
        private readonly Pdf                 $snappy,
        private readonly ModelFinder         $modelFinder
    )
    {
        $this->useRefreshAgentForRefreshSignature = SettingManager::get('setting.folder.useRefreshAgentForRefreshSignature.name');
    }

    #[Route('{id}/letter-access/print', name: 'lucca_control_access_print', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function accessLetterPrintAction(#[MapEntity(id: 'minute_id')] Minute $minute, Control $control): Response
    {
        $filename = sprintf('Access - %s', $minute->getNum());

        /** Get adherent and model corresponding*/
        $adherent = $minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->modelFinder->findModel(Model::DOCUMENTS_ACCESS_LETTER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $pdf = $this->generatePdfAccess($model, $minute, $control);

        /** We need to check if the pdf is correctly generated or not in order to be able to redirect to minute if needed */
        if ($pdf != null) {
            return new Response(
                $pdf, 200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'
                )
            );
        } else {
            $this->addFlash('danger', 'flash.pdf.cantBeCreated');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }
    }

    /**
     * Function use to generate pdf for access
     *
     * @param Model $p_model
     * @param Minute $p_minute
     * @param Control $p_control
     * @return string
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     * @throws PdfReaderException
     */
    private function generatePdfAccess(Model $p_model, Minute $p_minute, Control $p_control): ?string
    {
        /** Init var with tools need to merge pdf */
        $pdf = new PdfCollection();
        $filesystem = new Filesystem();
        $fpdi = new Fpdi();
        $merger = new PdfMerger($fpdi);
        $snappy = $this->snappy;

        if ($this->useRefreshAgentForRefreshSignature)
            $agent = $p_control->getAgent();
        else
            $agent = $p_minute->getAgent();


        /** Init var with data useful in loop */
        $adherent = $p_minute->getAdherent();

        $date = $p_control->getDatePostal() ?? new \DateTime();

        /** Init empty array in order to delete temp files when finish */
        $filesNames = array();

        /** Store filename that will be used for the final pdf */
        $filename = sprintf('Lettre de Convocation - %s', $p_minute->getNum());

        /** Path where the temp pdf are stored */
        $path = $this->getParameter('lucca_media.upload_tmp_directory') . 'pdfToPrint/';

        /** If the temp directory doesn't exist create it */
        if (!$filesystem->exists($path))
            $filesystem->mkdir($path);

        /** Create var array that will change aspect of header */
        $var = [
            "date" => $date->format("d/m/Y"),
            "place" => $adherent->getUnitAttachment(),
            "adherentName" => $adherent->getOfficialName(),
            "adherentMail" => $adherent->getEmailPublic(),
            "adherentTel" => $adherent->getOfficialPhone(),
            "structureName" => $adherent->getStructureName(),
            "structureOffice" => $adherent->getStructureOffice(),
            "agentName" => $agent->getOfficialName(),
        ];

        /** Create the model option in loop in order to be able to interact with header */
        $options = $this->pagePrinter->createModelOption($p_model, $var, $adherent);

        /** If there is edition with edition template */
        if (count($p_control->getEditions()) > 0) {
            /** Create html for current edition */

            /** For each edition generate a specific pdf */
            foreach ($p_control->getEditions() as $edition) {

                $html = $this->renderView('@LuccaMinute/Control/Printing/Basic/access_edition_print.html.twig', array(
                    'model' => $p_model, 'minute' => $p_minute, 'adherent' => $p_minute->getAdherent(), 'control' => $p_control, 'edition' => $edition, 'agent' => $agent
                ));

                /** Generate pdf from html */
                $generatedPdf = $snappy->getOutputFromHtml($html, $options);

                /** Store file path to create temp file and be able to delete it later */
                $filePath = $path . 'convocation-' . $edition->getId() . '.pdf';
                $filesNames[] = $filePath;

                /** Store file in temp folder */
                $filesystem->appendToFile($filePath, $generatedPdf);

                /** Add pdf to the final var */
                $pdf->addPDF($filePath, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);
            }

        } else {
            /** Create an array containing for which we need to create a convocation letter */
            $humans = array_unique(array_merge($p_control->getHumansByMinute()->toArray(), $p_control->getHumansByControl()->toArray()), SORT_REGULAR);

            /** Create a temp pdf for each human */
            foreach ($humans as $human) {
                /** Create html for current human */
                $html = $this->renderView('@LuccaMinute/Control/Printing/Basic/access_basic_print.html.twig', [
                    'model' => $p_model, 'minute' => $p_minute, 'adherent' => $p_minute->getAdherent(), 'human' => $human, 'control' => $p_control, 'agent' => $agent
                ]);
                /** Generate pdf from html */
                $generatedPdf = $snappy->getOutputFromHtml($html, $options);
                /** Store file path to create temp file and be able to delete it later */
                $filePath = $path . 'convocation-' . $human->getId() . '.pdf';
                $filesNames[] = $filePath;

                /** Store file in temp folder */
                $filesystem->appendToFile($filePath, $generatedPdf);

                /** Add pdf to the final var */
                $pdf->addPDF($filePath, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);
            }
        }
        if ($pdf->hasPdfs()) {
            /** Merge all the pdf into a final one, get a string in order to be able to return it to the main function */
            $merged = $merger->merge($pdf, $filename, PdfMerger::MODE_STRING);

            /** Then delete the temp files */
            foreach ($filesNames as $name) {
                $filesystem->remove($name);
            }
        } else {
            $merged = null;
        }

        return $merged;
    }


    #[Route('{id}/letter-convocation/print', name: 'lucca_control_letter_print', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function convocationLetterPrintAction(#[MapEntity(id: 'minute_id')] Minute $minute, Control $control): Response
    {
        /** Get adherent and model corresponding*/
        $adherent = $minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->modelFinder->findModel(Model::DOCUMENTS_CONVOCATION_LETTER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $filename = sprintf('Lettre de Convocation - %s', $minute->getNum());
        $pdf = $this->generatePdfConvocation($model, $minute, $control);

        /** We need to check if the pdf is correctly generated or not in order to be able to redirect to minute if needed */
        if ($pdf != null) {
            return new Response(
                $pdf, 200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'
                )
            );
        } else {
            $this->addFlash('danger', 'flash.pdf.cantBeCreated');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }
    }

    /**
     * Function use to generate pdf for convocation
     *
     * @param Model $p_model
     * @param Minute $p_minute
     * @param Control $p_control
     * @return string|null
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     * @throws PdfReaderException
     */
    private function generatePdfConvocation(Model $p_model, Minute $p_minute, Control $p_control): ?string
    {
        /** Init var with tools need to merge pdf */
        $pdf = new PdfCollection();
        $filesystem = new Filesystem();
        $fpdi = new Fpdi();
        $merger = new PdfMerger($fpdi);
        $snappy = $this->snappy;

        /** Init var with data useful in loop */
        $adherent = $p_minute->getAdherent();

        $date = $p_control->getDatePostal() ?? new \DateTime();

        /** Init empty array in order to delete temp files when finish */
        $filesNames = array();

        /** Store filename that will be used for the final pdf */
        $filename = sprintf('Lettre de Convocation - %s', $p_minute->getNum());

        /** Path where the temp pdf are stored */
        $path = $this->getParameter('lucca_media.upload_tmp_directory') . 'pdfToPrint/';

        /** If the temp directory doesn't exist create it */
        if (!$filesystem->exists($path))
            $filesystem->mkdir($path);

        /** Create var array that will change aspect of header */
        $var = [
            "date" => $date->format("d/m/Y"),
            "place" => $adherent->getUnitAttachment(),
            "adherentName" => $adherent->getOfficialName(),
            "adherentMail" => $adherent->getEmailPublic(),
            "adherentPhone" => $adherent->getOfficialPhone(),
            "structureName" => $adherent->getStructureName(),
            "structureOffice" => $adherent->getStructureOffice(),
            "unitAttachment" => $adherent->getUnitAttachment(),
            "agentName" => $p_minute->getAgent()->getOfficialName()
        ];

        /** If there is edition with edition template */
        if (count($p_control->getEditions()) > 0) {

            /** For each edition generate a specific pdf */
            /** @var ControlEdition $edition */
            foreach ($p_control->getEditions() as $edition) {
                $human = $edition->getHuman();

                $var['placeControl'] = $edition->getControl()->getMinute()->getPlot()->getTown()->getName();
                $var['humanGender'] = $this->translator->trans($human->getGender(), [], 'MinuteBundle');
                $var['humanName'] = $human->getOfficialName();
                $var['humanAddress'] = $human->getAddress();
                $var['humanCompany'] = "";
                if ($human->getPerson() == Human::PERSON_CORPORATION) {
                    $var['humanCompany'] = $human->getCompany();
                }

                /** Create the model option in loop to be able to interact with header */
                $options = $this->pagePrinter->createModelOption($p_model, $var, $adherent);

                /** Create html for current edition */
                $html = $this->renderView('@LuccaMinute/Control/Printing/Basic/convocation_edition_print.html.twig', [
                    'model' => $p_model, 'minute' => $p_minute, 'adherent' => $p_minute->getAdherent(), 'edition' => $edition, 'control' => $p_control
                ]);
                /** Generate pdf from html */
                $generatedPdf = $snappy->getOutputFromHtml($html, $options);
                /** Store file path to create temp file and be able to delete it later */
                $filePath = $path . 'convocation-' . $edition->getId() . '.pdf';
                $filesNames[] = $filePath;

                /** Store file in temp folder */
                $filesystem->appendToFile($filePath, $generatedPdf);

                /** Add pdf to the final var */
                $pdf->addPDF($filePath, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);
            }
        } else {
            /** Create an array containing for which we need to create a convocation letter */
            $humans = array_unique(array_merge($p_control->getHumansByMinute()->toArray(), $p_control->getHumansByControl()->toArray()), SORT_REGULAR);
            /** Create a temp pdf for each human */
            foreach ($humans as $human) {

                $var['placeControl'] = $p_control->getMinute()->getPlot()->getTown()->getName();
                $var['humanGender'] = $this->translator->trans($human->getGender(), [], 'MinuteBundle');
                $var['humanName'] = $human->getOfficialName();
                $var['humanAddress'] = $human->getAddress();
                $var['humanCompany'] = "";
                if ($human->getPerson() == Human::PERSON_CORPORATION) {
                    $var['humanCompany'] = $human->getCompany();
                }

                /** Create the model option in loop to be able to interact with header */
                $options = $this->pagePrinter->createModelOption($p_model, $var, $adherent);

                /** Create html for current human */
                $html = $this->renderView('@LuccaMinute/Control/Printing/Basic/convocation_basic_print.html.twig', [
                    'model' => $p_model, 'minute' => $p_minute, 'adherent' => $p_minute->getAdherent(), 'human' => $human, 'control' => $p_control
                ]);
                /** Generate pdf from html */
                $generatedPdf = $snappy->getOutputFromHtml($html, $options);
                /** Store file path to create temp file and be able to delete it later */
                $filePath = $path . 'convocation-' . $human->getId() . '.pdf';
                $filesNames[] = $filePath;

                /** Store file in temp folder */
                $filesystem->appendToFile($filePath, $generatedPdf);

                /** Add pdf to the final var */
                $pdf->addPDF($filePath, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);
            }
        }

        if ($pdf->hasPdfs()) {
            /** Merge all the pdf into a final one, get a string in order to be able to return it to the main function */
            $merged = $merger->merge($pdf, $filename, PdfMerger::MODE_STRING);

            /** Then delete the temp files */
            foreach ($filesNames as $name) {
                $filesystem->remove($name);
            }
        } else {
            $merged = null;
        }

        return $merged;
    }
}
