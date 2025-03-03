<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\ManualEdit;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Courier;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\FolderBundle\Form\Courier\CourierEditionDdtmType;
use Lucca\Bundle\FolderBundle\Form\Courier\CourierEditionJudicialType;
use Lucca\Bundle\FolderBundle\Form\Courier\CourierOffenderType;
use Lucca\Bundle\SettingBundle\Utils\SettingManager;
/**
 * Class CourierController
 *
 * @package Lucca\Bundle\FolderBundle\Controller\ManualEdit
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizée Meyer <alizee.m@numeric-wave.eu>
 */
#[Route('/minute-{minute_id}/courier-')]
#[IsGranted('ROLE_LUCCA')]
class CourierController extends AbstractController
{
    /**
     * Update Judicial letter of Courier
     *
     * @param Request $request
     * @param Minute $minute
     * @param Courier $courier
     * @return RedirectResponse|Response
     */
    #[Route('{id}/edit-judicial', name: 'lucca_courier_manual_judicial', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function manualEditingJudicialAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'id')] Courier $courier
    ): RedirectResponse|Response {
        if ($courier->getDateJudicial()) {
            $this->addFlash('warning', 'flash.courier.alreadySended');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();

        /** Define Logo and set margin */
        $logo = $this->get('lucca.utils.printer.control')->defineLogo($minute->getAdherent());

        $edition = $courier->getEdition();

        /** Fill empty edition with basic view */
        if (!$edition->getJudicialEdited()) {
            /** Check setting in order to display custom content if exist */
            if (SettingManager::get('setting.courier.judicialContent.name')) {
                $edition->setLetterJudicial(
                    $this->renderView(
                        '@LuccaFolder/Courier/Printing/Custom/judicial_content-' . SettingManager::get('setting.general.departement.name') . '.html.twig',
                        array(
                            'minute' => $minute,
                            'courier' => $courier,
                            'adherent' => $minute->getAdherent(),
                            'officialLogo' => $logo
                        ))
                );
            } else {
                $edition->setLetterJudicial(
                    $this->renderView('@LuccaFolder/Courier/Printing/Basic/judicial_content.html.twig', array(
                        'minute' => $minute,
                        'courier' => $courier,
                        'adherent' => $minute->getAdherent(),
                        'officialLogo' => $logo
                    ))
                );
            }
        }

        $form = $this->createForm(CourierEditionJudicialType::class, $edition, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean letter content if boolean is false */
            if (!$edition->getJudicialEdited())
                $edition->setLetterJudicial(null);
            else {
                /** Clean html from useless font and empty span */
                $edition->setLetterJudicial($this->get('lucca.utils.html_cleaner')->removeAllFonts($edition->getLetterJudicial()));
            }

            $em->persist($courier);
            $em->flush();

            $this->addFlash('success', 'flash.courier.judicialEditedManually');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('@LuccaFolder/CourierEdition/editJudicial.html.twig', array(
            'minute' => $minute,
            'courier' => $courier,
            'edition' => $edition,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Update Ddtm letter of Courier
     *
     * @param Request $request
     * @param Minute $minute
     * @param Courier $courier
     * @return RedirectResponse|Response
     */
    #[Route('{id}/edit-ddtm', name: 'lucca_courier_manual_ddtm', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function manualEditingDdtmAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'id')] Courier $courier
    ): RedirectResponse|Response {
        if ($courier->getDateDdtm()) {
            $this->addFlash('warning', 'flash.courier.alreadySended');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();

        $logo = $this->get('lucca.utils.printer.control')->defineLogo($minute->getAdherent());

        $edition = $courier->getEdition();

        /** Fill empty edition with basic view */
        if (!$edition->getDdtmEdited()) {
            /** Check setting in order to display custom content if exist */
            if (SettingManager::get('setting.courier.ddtmContent.name')) {
                $edition->setLetterDdtm(
                    $this->renderView(
                        '@LuccaFolder/Courier/Printing/Custom/ddtm_content-' . SettingManager::get('setting.general.departement.name') . '.html.twig',
                        array(
                            'minute' => $minute,
                            'courier' => $courier,
                            'adherent' => $minute->getAdherent(),
                            'officialLogo' => $logo
                        ))
                );
            } else {
                $edition->setLetterDdtm(
                    $this->renderView('@LuccaFolder/Courier/Printing/Basic/ddtm_content.html.twig', array(
                        'minute' => $minute,
                        'courier' => $courier,
                        'adherent' => $minute->getAdherent(),
                        'officialLogo' => $logo,
                    ))
                );
            }
        }

        $form = $this->createForm(CourierEditionDdtmType::class, $edition, array());
        $form->handleRequest($request);

        $logo = $this->get('lucca.utils.printer.control')->defineLogo($minute->getAdherent());

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean letter content if boolean is false */
            if (!$edition->getDdtmEdited())
                $edition->setLetterDdtm(null);
            else {
                /** Clean html from useless font and empty span */
                $edition->setLetterDdtm($this->get('lucca.utils.html_cleaner')->removeAllFonts($edition->getLetterDdtm()));
            }

            $em->persist($courier);
            $em->flush();

            $this->addFlash('success', 'flash.courier.ddtmEditedManually');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('@LuccaFolder/CourierEdition/editDdtm.html.twig', array(
            'minute' => $minute,
            'courier' => $courier,
            'edition' => $edition,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Update Offender letter of Courier
     *
     * @param Request $request
     * @param Minute $minute
     * @param Courier $courier
     * @return RedirectResponse|Response
     */
    #[Route('{id}/edit-offender', name: 'lucca_courier_manual_offender', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function letterOffenderAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'id')] Courier $courier
    ): RedirectResponse|Response {
        if ($courier->getDateOffender()) {
            $this->addFlash('warning', 'flash.courier.alreadySended');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $logo = $this->get('lucca.utils.printer.control')->defineLogo($minute->getAdherent());

        /** Fill all empty editions with basic view */
        foreach ($courier->getHumansEditions() as $edition) {

            /** Fill empty edition with basic view */
            if (!$edition->getLetterOffenderEdited()) {
                /** Check setting in order to display custom content if exist */
                if (SettingManager::get('setting.courier.offenderContent.name')) {
                    $edition->setLetterOffender(
                        $this->renderView(
                            '@LuccaFolder/Courier/Printing/Custom/offender_content-' . SettingManager::get('setting.general.departement.name') . '.html.twig',
                            array(
                                'minute' => $minute,
                                'adherent' => $minute->getAdherent(),
                                'courier' => $courier,
                                'human' => $edition->getHuman(),
                                'officialLogo' => $logo
                            ))
                    );
                } else {
                    $edition->setLetterOffender(
                        $this->renderView('@LuccaFolder/Courier/Printing/Basic/offender_content.html.twig', array(
                            'minute' => $minute,
                            'adherent' => $minute->getAdherent(),
                            'courier' => $courier,
                            'human' => $edition->getHuman(),
                            'officialLogo' => $logo
                        ))
                    );
                }
            }
        }

        $form = $this->createForm(CourierOffenderType::class, $courier, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean letter content if boolean is false */
            foreach ($courier->getHumansEditions() as $edition) {
                if (!$edition->getLetterOffenderEdited())
                    $edition->setLetterOffender(null);
                else {
                    /** Clean html from useless font and empty span */
                    $edition->setLetterOffender($this->get('lucca.utils.html_cleaner')->removeAllFonts($edition->getLetterOffender()));
                }
            }

            $em->persist($courier);
            $em->flush();

            $this->addFlash('success', 'flash.courier.offenderEditedManually');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('@LuccaFolder/CourierEdition/editOffender.html.twig', array(
            'minute' => $minute,
            'courier' => $courier,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ));
    }
}