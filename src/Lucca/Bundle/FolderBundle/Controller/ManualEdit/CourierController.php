<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\ManualEdit;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\FolderBundle\Entity\Courier;
use Lucca\Bundle\FolderBundle\Form\Courier\{CourierEditionDdtmType, CourierEditionJudicialType, CourierOffenderType};
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Printer\ControlPrinter;
use Lucca\Bundle\MinuteBundle\Utils\HtmlCleaner;
use Lucca\Bundle\SettingBundle\Utils\SettingManager;

#[Route(path: '/minute-{minute_id}/courier-')]
#[IsGranted('ROLE_LUCCA')]
class CourierController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ControlPrinter $controlPrinter,
        private readonly HtmlCleaner $htmlCleaner,
    )
    {
    }

    /**
     * Update Judicial letter of Courier
     */
    #[Route(path: '{id}/edit-judicial', name: 'lucca_courier_manual_judicial', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function manualEditingJudicialAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Courier $courier
    ): Response {
        if ($courier->getDateJudicial()) {
            $this->addFlash('warning', 'flash.courier.alreadySended');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        /** Define Logo and set margin */
        $logo = $this->controlPrinter->defineLogo($minute->getAdherent());

        $edition = $courier->getEdition();

        /** Fill empty edition with basic view */
        if (!$edition->getJudicialEdited()) {
            /** Check setting in order to display custom content if exist */
            if (SettingManager::get('setting.courier.judicialContent.name')) {
                $edition->setLetterJudicial(
                    $this->renderView(
                        '@LuccaFolder/Courier/Printing/Custom/judicial_content-' . SettingManager::get('setting.general.departement.name') . '.html.twig',
                        [
                            'minute' => $minute,
                            'courier' => $courier,
                            'adherent' => $minute->getAdherent(),
                            'officialLogo' => $logo
                        ])
                );
            } else {
                $edition->setLetterJudicial(
                    $this->renderView('@LuccaFolder/Courier/Printing/Basic/judicial_content.html.twig', [
                        'minute' => $minute,
                        'courier' => $courier,
                        'adherent' => $minute->getAdherent(),
                        'officialLogo' => $logo
                    ])
                );
            }
        }

        $form = $this->createForm(CourierEditionJudicialType::class, $edition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Clean letter content if boolean is false */
            if (!$edition->getJudicialEdited()) {
                $edition->setLetterJudicial(null);
            }
            else {
                /** Clean html from useless font and empty span */
                $edition->setLetterJudicial($this->htmlCleaner->removeAllFonts($edition->getLetterJudicial()));
            }

            $this->em->persist($courier);
            $this->em->flush();

            $this->addFlash('success', 'flash.courier.judicialEditedManually');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return $this->render('@LuccaFolder/CourierEdition/editJudicial.html.twig', [
            'minute' => $minute,
            'courier' => $courier,
            'edition' => $edition,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Update Ddtm letter of Courier
     */
    #[Route(path: '{id}/edit-ddtm', name: 'lucca_courier_manual_ddtm', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function manualEditingDdtmAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Courier $courier
    ): Response {
        if ($courier->getDateDdtm()) {
            $this->addFlash('warning', 'flash.courier.alreadySended');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        $logo = $this->controlPrinter->defineLogo($minute->getAdherent());

        $edition = $courier->getEdition();

        /** Fill empty edition with basic view */
        if (!$edition->getDdtmEdited()) {
            /** Check setting in order to display custom content if exist */
            if (SettingManager::get('setting.courier.ddtmContent.name')) {
                $edition->setLetterDdtm(
                    $this->renderView(
                        '@LuccaFolder/Courier/Printing/Custom/ddtm_content-' . SettingManager::get('setting.general.departement.name') . '.html.twig',
                        [
                            'minute' => $minute,
                            'courier' => $courier,
                            'adherent' => $minute->getAdherent(),
                            'officialLogo' => $logo
                        ])
                );
            } else {
                $edition->setLetterDdtm(
                    $this->renderView('@LuccaFolder/Courier/Printing/Basic/ddtm_content.html.twig', [
                        'minute' => $minute,
                        'courier' => $courier,
                        'adherent' => $minute->getAdherent(),
                        'officialLogo' => $logo,
                    ])
                );
            }
        }

        $form = $this->createForm(CourierEditionDdtmType::class, $edition);
        $form->handleRequest($request);

        $logo = $this->controlPrinter->defineLogo($minute->getAdherent());

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean letter content if boolean is false */
            if (!$edition->getDdtmEdited()) {
                $edition->setLetterDdtm(null);
            }
            else {
                /** Clean html from useless font and empty span */
                $edition->setLetterDdtm($this-$this->htmlCleaner->removeAllFonts($edition->getLetterDdtm()));
            }

            $this->em->persist($courier);
            $this->em->flush();

            $this->addFlash('success', 'flash.courier.ddtmEditedManually');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return $this->render('@LuccaFolder/CourierEdition/editDdtm.html.twig', [
            'minute' => $minute,
            'courier' => $courier,
            'edition' => $edition,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Update Offender letter of Courier
     */
    #[Route(path: '{id}/edit-offender', name: 'lucca_courier_manual_offender', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function letterOffenderAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Courier $courier
    ): Response {
        if ($courier->getDateOffender()) {
            $this->addFlash('warning', 'flash.courier.alreadySended');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        $logo = $this->controlPrinter->defineLogo($minute->getAdherent());

        /** Fill all empty editions with basic view */
        foreach ($courier->getHumansEditions() as $edition) {

            /** Fill empty edition with basic view */
            if (!$edition->getLetterOffenderEdited()) {
                /** Check setting in order to display custom content if exist */
                if (SettingManager::get('setting.courier.offenderContent.name')) {
                    $edition->setLetterOffender(
                        $this->renderView(
                            '@LuccaFolder/Courier/Printing/Custom/offender_content-' . SettingManager::get('setting.general.departement.name') . '.html.twig',
                            [
                                'minute' => $minute,
                                'adherent' => $minute->getAdherent(),
                                'courier' => $courier,
                                'human' => $edition->getHuman(),
                                'officialLogo' => $logo
                            ])
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

        $form = $this->createForm(CourierOffenderType::class, $courier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** Clean letter content if boolean is false */
            foreach ($courier->getHumansEditions() as $edition) {
                if (!$edition->getLetterOffenderEdited()) {
                    $edition->setLetterOffender(null);
                }
                else {
                    /** Clean html from useless font and empty span */
                    $edition->setLetterOffender($this->htmlCleaner->removeAllFonts($edition->getLetterOffender()));
                }
            }

            $this->em->persist($courier);
            $this->em->flush();

            $this->addFlash('success', 'flash.courier.offenderEditedManually');

            return $this->redirectToRoute('lucca_minute_show', ['id' => $minute->getId()]);
        }

        return $this->render('@LuccaFolder/CourierEdition/editOffender.html.twig', [
            'minute' => $minute,
            'courier' => $courier,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ]);
    }
}
