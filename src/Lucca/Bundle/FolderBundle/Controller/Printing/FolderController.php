<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Printing;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Exception;
use Lucca\ModelBundle\Entity\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FolderController
 *
 * @Route("/minute-{minute_id}/folder-")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Folderler\Printing
 * @author Terence <terence@numeric-wave.tech>
 */
class FolderController extends Controller
{
    /**
     * Print a Folder
     *
     * @Route("{id}/document/print", name="lucca_folder_doc_print", methods={"GET"})
     * @Route("{id}/document/preprint", name="lucca_folder_doc_preprint", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @param Folder $folder
     * @param Request $p_request
     * @return Response
     */
    public function folderDocPrintAction(Minute $minute, Folder $folder, Request $p_request)
    {
        /** Check in route if it's preprint route that is called */
        $isPrePrint = str_contains($p_request->attributes->get('_route'), "preprint");

        /** Step 1 : init snappy */
        $snappy = $this->get('knp_snappy.pdf');
        $em = $this->getDoctrine()->getManager();

        $filename = sprintf('Folder %s', $folder->getNum());

        $update = $em->getRepository('LuccaMinuteBundle:Updating')->findUpdatingByControl($folder->getControl());

        /** Step 2 : Get adherent and model corresponding*/
        $adherent = $minute->getAdherent();

        /** Try to find the model corresponding to the document */
        $model = $this->get('lucca.finder.model')->findModel(Model::DOCUMENTS_FOLDER, $adherent);

        /** If model is null it's mean there is no model created, so we can't generate the PDF */
        if ($model === null) {
            $this->addFlash('danger', 'flash.model.notFound');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }


        /** Step 3 : Create html */
        try {
            $html = $this->renderView('@LuccaMinute/Folder/Printing/Basic/doc_print.html.twig', [
                'model' => $model, 'minute' => $minute, 'adherent' => $minute->getAdherent(), 'folder' => $folder, 'update' => $update, 'isPreprint' => $isPrePrint
            ]);
        } catch (\Twig\Error\Error $twig_Error) {
            $html = null;
            echo 'Twig_Error has been thrown - Body ' . $twig_Error->getMessage();
        }

        $var = [
            "date" => (new \DateTime("now"))->format("d/m/Y"),
            "structureOffice" => $adherent->getStructureOffice(),
            "adherentName" => $adherent->getOfficialName(),
            "adherentMail" => $adherent->getEmailPublic(),
            "adherentPhone" => $adherent->getOfficialPhone(),
            "agentName" => $folder->getMinute()->getAgent()->getOfficialName()
        ];

        $options = $this->get('lucca.utils.printer.model.page')->createModelOption($model, $var, $adherent);

        try {
            $generatedPdf = $snappy->getOutputFromHtml($html, $options);
        } catch (Exception $e) {
            $this->addFlash('danger', 'flash.folder.cannotPrint');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return new Response(
            $generatedPdf, 200,
            array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"')
        );
    }
}
