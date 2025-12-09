<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Printing;

use DateTime;
use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use setasign\Fpdi\Fpdi;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tomsgu\PdfMerger\{PdfCollection, PdfFile, PdfMerger};

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\FolderBundle\Entity\Courier;
use Lucca\Bundle\MinuteBundle\Entity\{Human, Minute};
use Lucca\Bundle\ModelBundle\Entity\Model;
use Lucca\Bundle\ModelBundle\Printer\PagePrinter;
use Lucca\Bundle\ModelBundle\Service\ModelFinder;

#[Route(path: '/minute-{minute_id}/courier')]
#[IsGranted('ROLE_LUCCA')]
class CourierController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ModelFinder            $modelFinder,
        private readonly PagePrinter            $pagePrinter,
        private readonly Pdf                    $pdf,
        private readonly TranslatorInterface    $translator,
    )
    {
    }

    /**
     * Offender Letter print
     *
     * @throws Exception
     */
    #[Route(path: '-{id}/offender-print', name: 'lucca_courier_offender_print', methods: ['GET', 'POST'])]
    #[Route(path: '-{id}/offender-preprint', name: 'lucca_courier_offender_preprint', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function offenderPrintAction(
        #[MapEntity(id: 'minute_id')] Minute    $minute,
        Courier                                 $courier,
        Request                                 $request
    ): Response {
        /** Check in route if it's preprint route that is called */
        $isPrePrint = str_contains($request->attributes->get('_route'), "preprint");

        /** If Courier is not dated - return on Minute */
        if (!$courier->getDateOffender() && !$isPrePrint) {
            $courier->setDateOffender(new DateTime('now'));

            $this->em->persist($courier);
            $this->em->flush();

            $this->addFlash('success', 'flash.courier.offenderDate');

            return $this->redirectToRoute('lucca_minute_show', [
                'id' => $minute->getId(),
                '_fragment' => 'courier-' . $courier->getId(),
            ]);
        }

        $filename = sprintf('Lettres aux contrevant du PV - %s', $courier->getFolder()->getNum());

        /** Get adherent and model corresponding*/
        $adherent = $minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->modelFinder->findModel(Model::DOCUMENTS_OFFENDER_LETTER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return new Response(
            $this->generatePdfoffender($model, $minute, $courier, $isPrePrint), 200,
            [
                'Content-Type' => 'application/pdf',
                ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"']
            ]
        );
    }

    /**
     * Function use to generate pdf for Offender
     *
     * @throws Exception
     */
    private function generatePdfOffender(Model $model, Minute $minute, Courier $courier, bool $isPrePrint): string
    {
        /** Init var with tools need to merge pdf */
        $pdf = new PdfCollection();
        $filesystem = new Filesystem();
        $fpdi = new Fpdi();
        $merger = new PdfMerger($fpdi);

        /** Init var with data useful in loop */
        $adherent = $minute->getAdherent();
        $date = $courier->getDateOffender() ?? new DateTime();

        /** Init empty array in order to delete temp files when finish */
        $filesNames = array();

        /** Store filename that will be used for the final pdf */
        $filename = sprintf('Lettre de Convocation - %s', $minute->getNum());

        /** Path where the temp pdf are stored */
        $path = $this->getParameter('lucca_media.upload_tmp_directory') . 'pdfToPrint/';

        /** If the temp directory doesn't exist create it */
        if (!$filesystem->exists($path)) {
            $filesystem->mkdir($path);
        }

        /** Create var array that will change aspect of header */
        $var = [
            "date" => $date->format("d/m/Y"),
            "place" => $adherent->getUnitAttachment(),
            "adherentName" => $adherent->getOfficialName(),
            "adherentMail" => $adherent->getEmailPublic(),
            "adherentPhone" => $adherent->getOfficialPhone(),
            "structureName" => $adherent->getStructureName(),
            "structureOffice" => $adherent->getStructureOffice(),
            "agentName" => $minute->getAgent()->getOfficialName()
        ];

        /** If there is edition with edition template */
        if (count($courier->getHumansEditions()) > 0) {

            /** Create html for current edition */

            /** For each edition generate a specific pdf */
            foreach ($courier->getHumansEditions() as $edition) {

                $human = $edition->getHuman();

                $var['humanGender'] = $this->translator->trans($human->getGender(), [], 'FolderBundle');
                $var['humanName'] = $human->getOfficialName();
                $var['humanAddress'] = $human->getAddress();
                $var['humanCompany'] = "";
                if ($human->getPerson() == Human::PERSON_CORPORATION) {
                    $var['humanCompany'] = $human->getCompany();
                }

                /** Create the model option in loop in order to be able to interact with header */
                $options = $this->pagePrinter->createModelOption($model, $var, $adherent);

                $html = $this->renderView('@LuccaFolder/Courier/Printing/Basic/offender_edition_print.html.twig', [
                    'model' => $model, 'minute' => $minute, 'adherent' => $minute->getAdherent(),
                    'courier' => $courier, 'edition' => $edition, 'isPreprint' => $isPrePrint,
                ]);


                /** Generate pdf from html */
                $generatedPdf = $this->pdf->getOutputFromHtml($html, $options);

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
            $humans = array_unique(array_merge($courier->getFolder()->getHumansByMinute()->toArray(), $courier->getFolder()->getHumansByMinute()->toArray()), SORT_REGULAR);
            /** Create a temp pdf for each human */
            foreach ($humans as $human) {

                $var['humanGender'] = $this->translator->trans($human->getGender(), [], 'FolderBundle');
                $var['humanName'] = $human->getOfficialName();
                $var['humanAddress'] = $human->getAddress();
                $var['humanCompany'] = "";
                if ($human->getPerson() == Human::PERSON_CORPORATION) {
                    $var['humanCompany'] = $human->getCompany();
                }

                /** Create the model option in loop in order to be able to interact with header */
                $options = $this->pagePrinter->createModelOption($model, $var, $adherent);

                /** Create html for current human */
                $html = $this->renderView('@LuccaFolder/Courier/Printing/Basic/offender_basic_print.html.twig', [
                    'model' => $model, 'minute' => $minute, 'adherent' => $minute->getAdherent(),
                    'courier' => $courier, 'human' => $human, 'isPreprint' => $isPrePrint,
                ]);

                /** Generate pdf from html */
                $generatedPdf = $this->pdf->getOutputFromHtml($html, $options);

                /** Store file path to create temp file and be able to delete it later */
                $filePath = $path . 'convocation-' . $human->getId() . '.pdf';
                $filesNames[] = $filePath;

                /** Store file in temp folder */
                $filesystem->appendToFile($filePath, $generatedPdf);

                /** Add pdf to the final var */
                $pdf->addPDF($filePath, PdfFile::ALL_PAGES, PdfFile::ORIENTATION_PORTRAIT);
            }
        }

        /** Merge all the pdf into a final one, get a string in order to be able to return it to the main function */
        $merged = $merger->merge($pdf, $filename, PdfMerger::MODE_STRING);

        /** Then delete the temp files */
        foreach ($filesNames as $name) {
            $filesystem->remove($name);
        }

        return $merged;
    }


    /**
     * Judicial Print letter
     *
     * @throws Exception
     */
    #[Route(path: '-{id}/judicial-print', name: 'lucca_courier_judicial_print', methods: ['GET', 'POST'])]
    #[Route(path: '-{id}/judicial-preprint', name: 'lucca_courier_judicial_preprint', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function judicialPrintAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Courier                              $courier,
        Request                              $request
    ): Response {
        /** Check in route if it's preprint route that is called */
        $isPrePrint = str_contains($request->attributes->get('_route'), "preprint");

        /** If Courier is not dated - return on Minute */
        if (!$courier->getDateJudicial() && !$isPrePrint) {

            $courier->setDateJudicial(new \DateTime('now'));

            $this->em->persist($courier);
            $this->em->flush();

            $this->addFlash('success', 'flash.courier.judicialDate');

            return $this->redirectToRoute('lucca_minute_show', [
                'id' => $minute->getId(),
                '_fragment' => 'courier-' . $courier->getId(),
            ]);
        }

        /** Step 1 : init snappy */
        $filename = sprintf('Lettres au parquet du PV - %s', $courier->getFolder()->getNum());

        /** Step 2 : Get adherent and model corresponding*/
        $adherent = $minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->modelFinder->findModel(Model::DOCUMENTS_JUDICIAL_LETTER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        /** Step 3 : Create html */
        $html = $this->renderView('@LuccaFolder/Courier/Printing/Basic/judicial_print.html.twig', [
            'model' => $model, 'minute' => $minute, 'adherent' => $minute->getAdherent(), 'courier' => $courier, 'isPreprint' => $isPrePrint
        ]);

        $var = [
            "date" => (new DateTime("now"))->format("d/m/Y"),
            "dateJudicial" => $courier->getDateJudicial(),
            "structureOffice" => $adherent->getStructureOffice(),
            "tribunalAddress" => $minute->getTribunal()->getFullAddress(),
            "tribunalCompetentAddress" => $minute->getTribunalCompetent()->getFullAddress(),
            "numFolder" => $courier->getFolder()->getNum(),
            "adherentName" => $adherent->getOfficialName(),
            "adherentMail" => $adherent->getEmailPublic(),
            "adherentPhone" => $adherent->getOfficialPhone(),
            "agentName" => $minute->getAgent()->getOfficialName()
        ];

        if ($minute->getTribunal()->getInterlocutor() != null) {
            $var["tribunalInterlocutor"] = $minute->getTribunal()->getInterlocutor();
        } else {
            $var["tribunalInterlocutor"] = '';
        }

        /** Get only for country agent to be able to display right model for ddt66 */
        if ($adherent->getFunction() === Adherent::FUNCTION_COUNTRY_AGENT) {
            $var['unitRattachmentAgent'] = $adherent->getUnitAttachment();
        } else {
            $var['unitRattachmentAgent'] = '';
        }

        $var['unitRattachment'] = $adherent->getUnitAttachment();

        /** Get service of adherent only if exist */
        if ($adherent->getService() != null) {
            $var['service'] = $adherent->getService()->getName();
        } else {
            $var['service'] = '';
        }

        $options = $this->pagePrinter->createModelOption($model, $var, $adherent);

        $generatedPdf = $this->pdf->getOutputFromHtml($html, $options);

        return new Response(
            $generatedPdf, 200,
            ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"']
        );
    }

    /**
     * Ddtm Print letter
     *
     * @throws Exception
     */
    #[Route(path: '-{id}/ddtm-print', name: 'lucca_courier_ddtm_print', methods: ['GET', 'POST'])]
    #[Route(path: '-{id}/ddtm-preprint', name: 'lucca_courier_ddtm_preprint', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function ddtmPrintAction(
        #[MapEntity(id: 'minute_id')] Minute    $minute,
        Courier                                 $courier,
        Request                                 $request
    ): Response {
        /** Check in route if it's preprint route that is called */
        $isPrePrint = str_contains($request->attributes->get('_route'), "preprint");

        /** If Courier is not dated - return on Minute */
        if (!$courier->getDateDdtm() && !$isPrePrint) {
            $courier->setDateDdtm(new DateTime('now'));

            $this->em->persist($courier);
            $this->em->flush();

            $this->addFlash('success', 'flash.courier.ddtmDate');

            return $this->redirectToRoute('lucca_minute_show', [
                'id' => $minute->getId(),
                '_fragment' => 'courier-' . $courier->getId(),
            ]);
        }

        /** Step 1 : init snappy */
        $filename = sprintf('Lettres à la DDT du PV - %s', $courier->getFolder()->getNum());

        /** Step 2 : Get adherent and model corresponding*/
        $adherent = $minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->modelFinder->findModel(Model::DOCUMENTS_DDTM_LETTER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        $html = $this->renderView('@LuccaFolder/Courier/Printing/Basic/ddtm_print.html.twig', [
            'model' => $model, 'minute' => $minute, 'adherent' => $minute->getAdherent(), 'courier' => $courier, 'isPreprint' => $isPrePrint
        ]);

        if ($courier->getFolder()->getDateClosure()) {
            $date = $courier->getFolder()->getDateClosure()->format("d/m/Y");
        } else {
            $date = "";
        }

        $var = [
            "date" => $date,
            "dateDDTM" => $courier->getDateDdtm(),
            "structureOffice" => $adherent->getStructureOffice(),
            "tribunalAddress" => $minute->getTribunal()->getFullAddress(),
            "tribunalCompetentAddress" => $minute->getTribunalCompetent()->getFullAddress(),
            "numFolder" => $courier->getFolder()->getNum(),
            "adherentName" => $adherent->getOfficialName(),
            "adherentMail" => $adherent->getEmailPublic(),
            "adherentPhone" => $adherent->getOfficialPhone(),
            "agentName" => $minute->getAgent()->getOfficialName()
        ];

        $options = $this->pagePrinter->createModelOption($model, $var, $adherent);

        $generatedPdf = $this->pdf->getOutputFromHtml($html, $options);

        return new Response(
            $generatedPdf, 200,
            ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"']
        );
    }
}
