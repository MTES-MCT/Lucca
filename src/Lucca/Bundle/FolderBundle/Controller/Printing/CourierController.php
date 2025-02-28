<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Printing;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Lucca\AdherentBundle\Entity\Adherent;
use Lucca\ModelBundle\Entity\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use setasign\Fpdi\Fpdi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tomsgu\PdfMerger\PdfCollection;
use Tomsgu\PdfMerger\PdfFile;
use Tomsgu\PdfMerger\PdfMerger;

/**
 * Class CourierController
 *
 * @Route("/minute-{minute_id}/courier")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("p_minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Controller\Printing
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class CourierController extends Controller
{
    /**
     * Offender Letter print
     *
     * @Route("-{id}/offender-print", name="lucca_courier_offender_print", methods={"GET", "POST"})
     * @Route("-{id}/offender-preprint", name="lucca_courier_offender_preprint", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $p_minute
     * @param Courier $p_courier
     * @param Request $p_request
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function offenderPrintAction(Minute $p_minute, Courier $p_courier, Request $p_request)
    {
        /** Check in route if it's preprint route that is called */
        $isPrePrint = str_contains($p_request->attributes->get('_route'), "preprint");

        /** If Courier is not dated - return on Minute */
        if (!$p_courier->getDateOffender() && !$isPrePrint) {
            $em = $this->getDoctrine()->getManager();

            $p_courier->setDateOffender(new \DateTime('now'));

            $em->persist($p_courier);
            $em->flush();

            $this->addFlash('success', 'flash.courier.offenderDate');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $p_minute->getId(), '_fragment' => 'courier-' . $p_courier->getId()));
        }

        $filename = sprintf('Lettres aux contrevant du PV - %s', $p_courier->getFolder()->getNum());

        /** Get adherent and model corresponding*/
        $adherent = $p_minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->get('lucca.finder.model')->findModel(Model::DOCUMENTS_OFFENDER_LETTER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $p_minute->getId()));
        }

        return new Response(
            $this->generatePdfoffender($model, $p_minute, $p_courier, $isPrePrint), 200,
            array(
                'Content-Type' => 'application/pdf',
                array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"')
            )
        );
    }

    /**
     * Function use to generate pdf for Offender
     *
     * @param Model $p_model
     * @param Minute $p_minute
     * @param Courier $p_courier
     * @param bool $isPrePrint
     * @return string
     * @throws \Exception
     */
    private function generatePdfOffender(Model $p_model, Minute $p_minute, Courier $p_courier, bool $isPrePrint)
    {

        /** Init var with tools need to merge pdf */
        $pdf = new PdfCollection();
        $filesystem = new Filesystem();
        $fpdi = new Fpdi();
        $merger = new PdfMerger($fpdi);
        $snappy = $this->get('knp_snappy.pdf');

        /** Init var with data useful in loop */
        $adherent = $p_minute->getAdherent();
        $date = $p_courier->getDateOffender() ?? new \DateTime();

        /** Init empty array in order to delete temp files when finish */
        $filesNames = array();

        /** Store filename that will be used for the final pdf */
        $filename = sprintf('Lettre de Convocation - %s', $p_minute->getNum());

        /** Path where the temp pdf are stored */
        $path = $this->getParameter('env(LUCCA_UPLOAD_TEMP_DIR)') . 'pdfToPrint/';

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
            "agentName" => $p_minute->getAgent()->getOfficialName()
        ];

        /** If there is edition with edition template */
        if (count($p_courier->getHumansEditions()) > 0) {

            /** Create html for current edition */

            /** For each edition generate a specific pdf */
            foreach ($p_courier->getHumansEditions() as $edition) {

                $human = $edition->getHuman();

                $var['humanGender'] = $this->get("translator")->trans($human->getGender(), [], 'LuccaMinuteBundle');
                $var['humanName'] = $human->getOfficialName();
                $var['humanAddress'] = $human->getAddress();
                $var['humanCompany'] = "";
                if ($human->getPerson() == Human::PERSON_CORPORATION) {
                    $var['humanCompany'] = $human->getCompany();
                }

                /** Create the model option in loop in order to be able to interact with header */
                $options = $this->get('lucca.utils.printer.model.page')->createModelOption($p_model, $var, $adherent);

                $html = $this->renderView('@LuccaMinute/Courier/Printing/Basic/offender_edition_print.html.twig', [
                    'model' => $p_model, 'minute' => $p_minute, 'adherent' => $p_minute->getAdherent(),
                    'courier' => $p_courier, 'edition' => $edition, 'isPreprint' => $isPrePrint,
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
            $humans = array_unique(array_merge($p_courier->getFolder()->getHumansByMinute()->toArray(), $p_courier->getFolder()->getHumansByMinute()->toArray()), SORT_REGULAR);
            /** Create a temp pdf for each human */
            foreach ($humans as $human) {

                $var['humanGender'] = $this->get("translator")->trans($human->getGender(), [], 'LuccaMinuteBundle');
                $var['humanName'] = $human->getOfficialName();
                $var['humanAddress'] = $human->getAddress();
                $var['humanCompany'] = "";
                if ($human->getPerson() == Human::PERSON_CORPORATION) {
                    $var['humanCompany'] = $human->getCompany();
                }

                /** Create the model option in loop in order to be able to interact with header */
                $options = $this->get('lucca.utils.printer.model.page')->createModelOption($p_model, $var, $adherent);

                /** Create html for current human */
                $html = $this->renderView('@LuccaMinute/Courier/Printing/Basic/offender_basic_print.html.twig', [
                    'model' => $p_model, 'minute' => $p_minute, 'adherent' => $p_minute->getAdherent(),
                    'courier' => $p_courier, 'human' => $human, 'isPreprint' => $isPrePrint,
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
     * @Route("-{id}/judicial-print", name="lucca_courier_judicial_print", methods={"GET", "POST"})
     * @Route("-{id}/judicial-preprint", name="lucca_courier_judicial_preprint", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $p_minute
     * @param Courier $p_courier
     * @param Request $p_request
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function judicialPrintAction(Minute $p_minute, Courier $p_courier, Request $p_request)
    {
        $em = $this->getDoctrine()->getManager();

        /** Check in route if it's preprint route that is called */
        $isPrePrint = str_contains($p_request->attributes->get('_route'), "preprint");

        /** If Courier is not dated - return on Minute */
        if (!$p_courier->getDateJudicial() && !$isPrePrint) {

            $p_courier->setDateJudicial(new \DateTime('now'));

            $em->persist($p_courier);
            $em->flush();

            $this->addFlash('success', 'flash.courier.judicialDate');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $p_minute->getId(), '_fragment' => 'courier-' . $p_courier->getId()));
        }

        /** Step 1 : init snappy */
        $snappy = $this->get('knp_snappy.pdf');
        $filename = sprintf('Lettres au parquet du PV - %s', $p_courier->getFolder()->getNum());

        /** Step 2 : Get adherent and model corresponding*/
        $adherent = $p_minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->get('lucca.finder.model')->findModel(Model::DOCUMENTS_JUDICIAL_LETTER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $p_minute->getId()));
        }

        /** Step 3 : Create html */
        $html = $this->renderView('@LuccaMinute/Courier/Printing/Basic/judicial_print.html.twig', [
            'model' => $model, 'minute' => $p_minute, 'adherent' => $p_minute->getAdherent(), 'courier' => $p_courier, 'isPreprint' => $isPrePrint
        ]);

        $var = [
            "date" => (new \DateTime("now"))->format("d/m/Y"),
            "dateJudicial" => $p_courier->getDateJudicial(),
            "structureOffice" => $adherent->getStructureOffice(),
            "tribunalAddress" => $p_minute->getTribunal()->getFullAddress(),
            "numFolder" => $p_courier->getFolder()->getNum(),
            "adherentName" => $adherent->getOfficialName(),
            "adherentMail" => $adherent->getEmailPublic(),
            "adherentPhone" => $adherent->getOfficialPhone(),
            "agentName" => $p_minute->getAgent()->getOfficialName()
        ];

        if ($p_minute->getTribunal()->getInterlocutor() != null) {
            $var["tribunalInterlocutor"] = $p_minute->getTribunal()->getInterlocutor();
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

        $options = $this->get('lucca.utils.printer.model.page')->createModelOption($model, $var, $adherent);

        $generatedPdf = $snappy->getOutputFromHtml($html, $options);

        return new Response(
            $generatedPdf, 200,
            array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"')
        );
    }

    /**
     * Ddtm Print letter
     *
     * @Route("-{id}/ddtm-print", name="lucca_courier_ddtm_print", methods={"GET", "POST"})
     * @Route("-{id}/ddtm-preprint", name="lucca_courier_ddtm_preprint", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $p_minute
     * @param Courier $p_courier
     * @param Request $p_request
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function ddtmPrintAction(Minute $p_minute, Courier $p_courier, Request $p_request)
    {
        $em = $this->getDoctrine()->getManager();
        /** Check in route if it's preprint route that is called */
        $isPrePrint = str_contains($p_request->attributes->get('_route'), "preprint");

        /** If Courier is not dated - return on Minute */
        if (!$p_courier->getDateDdtm() && !$isPrePrint) {
            $em = $this->getDoctrine()->getManager();

            $p_courier->setDateDdtm(new \DateTime('now'));

            $em->persist($p_courier);
            $em->flush();

            $this->addFlash('success', 'flash.courier.ddtmDate');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $p_minute->getId(), '_fragment' => 'courier-' . $p_courier->getId()));
        }

        /** Step 1 : init snappy */
        $snappy = $this->get('knp_snappy.pdf');
        $filename = sprintf('Lettres à la DDT du PV - %s', $p_courier->getFolder()->getNum());

        /** Step 2 : Get adherent and model corresponding*/
        $adherent = $p_minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->get('lucca.finder.model')->findModel(Model::DOCUMENTS_DDTM_LETTER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $p_minute->getId()));
        }

        $html = $this->renderView('@LuccaMinute/Courier/Printing/Basic/ddtm_print.html.twig', [
            'model' => $model, 'minute' => $p_minute, 'adherent' => $p_minute->getAdherent(), 'courier' => $p_courier, 'isPreprint' => $isPrePrint
        ]);

        if ($p_courier->getFolder()->getDateClosure()) {
            $date = $p_courier->getFolder()->getDateClosure()->format("d/m/Y");
        } else {
            $date = "";
        }

        $var = [
            "date" => $date,
            "dateDDTM" => $p_courier->getDateDdtm(),
            "structureOffice" => $adherent->getStructureOffice(),
            "tribunalAddress" => $p_minute->getTribunal()->getFullAddress(),
            "numFolder" => $p_courier->getFolder()->getNum(),
            "adherentName" => $adherent->getOfficialName(),
            "adherentMail" => $adherent->getEmailPublic(),
            "adherentPhone" => $adherent->getOfficialPhone(),
            "agentName" => $p_minute->getAgent()->getOfficialName()
        ];

        $options = $this->get('lucca.utils.printer.model.page')->createModelOption($model, $var, $adherent);

        $generatedPdf = $snappy->getOutputFromHtml($html, $options);

        return new Response(
            $generatedPdf, 200,
            array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"')
        );
    }
}
