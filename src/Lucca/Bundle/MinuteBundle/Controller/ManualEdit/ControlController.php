<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\ManualEdit;

use Lucca\Bundle\MinuteBundle\Manager\ControlEditionManager;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\ControlEdition;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Form\Control\ControlAccessType;
use Lucca\Bundle\MinuteBundle\Form\Control\ControlConvocationType;
use Lucca\Bundle\MinuteBundle\Printer\ControlPrinter;
use Lucca\Bundle\MinuteBundle\Utils\HtmlCleaner;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/minute-{minute_id}/control-')]
#[IsGranted('ROLE_LUCCA')]
class ControlController extends AbstractController
{
    /** Setting if use agent of refresh or minute agent */
    private $useRefreshAgentForRefreshSignature;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ControlEditionManager  $controlEditionManager,
        private readonly HtmlCleaner            $htmlCleaner,
        private readonly ControlPrinter         $controlPrinter
    )
    {
        $this->useRefreshAgentForRefreshSignature = SettingManager::get('setting.folder.useRefreshAgentForRefreshSignature.name');
    }

    #[Route('{id}/letter-access/edit-manually', name: 'lucca_control_access_manual', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function accessLetterAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Control $control
    ): RedirectResponse|Response
    {
        $em = $this->entityManager;;

        /** If number of editions is different to nuymber of human - recreate editions */
        if ($control->getEditions()->count() !== ($control->getHumansByControl()->count() + $control->getHumansByMinute()->count())) {
            $this->controlEditionManager->manageEditionsOnFormSubmission($control);

            $this->addFlash('success', 'flash.control.updatedSuccessfully');
            $em->flush();
        }

        if ($this->useRefreshAgentForRefreshSignature)
            $agent = $control->getAgent();
        else
            $agent = $minute->getAgent();

        $logo = $this->controlPrinter->defineLogo($minute->getAdherent());

        /** Fill all empty editions with basic view */
        foreach ($control->getEditions() as $edition) {
            $docName = null;

            if (!$edition->getAccessEdited() && $control->getStateControl() === Control::STATE_NEIGHBOUR) {
                if (SettingManager::get('setting.control.accessEmpty.name')) {
                    $docName = '@LuccaMinute/Control/Printing/Custom:access_empty-' .
                        SettingManager::get('setting.general.departement.name') . '.html.twig';
                } else {
                    $docName = '@LuccaMinute/Control/Printing/Basic/access_empty.html.twig';
                }
            } elseif (!$edition->getAccessEdited()) {
                if (SettingManager::get('setting.control.accessContent.name')) {
                    $docName = '@LuccaMinute/Control/Printing/Custom/access_content-' .
                        SettingManager::get('setting.general.departement.name') . '.html.twig';
                } else {
                    $docName = '@LuccaMinute/Control/Printing/Basic/access_content.html.twig';
                }
            }

            if ($docName != null) {
                $edition->setLetterAccess(
                    $this->renderView($docName, array(
                        'agent' => $agent,
                        'minute' => $minute,
                        'adherent' => $minute->getAdherent(),
                        'control' => $control,
                        'human' => $edition->getHuman(),
                        'officialLogo' => $logo,
                    ))
                );
            }
        }

        $form = $this->createForm(ControlAccessType::class, $control, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean letter content if boolean is false */
            foreach ($control->getEditions() as $edition) {
                if (!$edition->getAccessEdited())
                    $edition->setLetterAccess(null);
                else {
                    /** Call service to clean all html of this step from useless fonts */
                    $edition->setLetterAccess($this->htmlCleaner->removeAllFonts($edition->getLetterAccess()));
                }
            }
            $em->persist($control);
            $em->flush();

            $this->addFlash('success', 'flash.control.access.updatedSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }


        return $this->render('@LuccaMinute/ControlEdition/letterAccess.html.twig', array(
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'control' => $control,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ));
    }

    #[Route('{id}/letter-convocation/edit-manually', name: 'lucca_control_convocation_manual', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function convocationLetterAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        Control $control
    ): RedirectResponse|Response
    {
        $em = $this->entityManager;;

        /** If number of editions is different to nuymber of human - recreate editions */
        if ($control->getEditions()->count() !== ($control->getHumansByControl()->count() + $control->getHumansByMinute()->count())) {
            $this->controlEditionManager->manageEditionsOnFormSubmission($control);

            $this->addFlash('success', 'flash.control.updatedSuccessfully');
            $em->flush();
        }
        $logo = $this->controlPrinter->defineLogo($minute->getAdherent());

        /** Fill all empty editions with basic view */
        /** @var ControlEdition $edition */
        foreach ($control->getEditions() as $edition) {
            if (!$edition->getConvocationEdited()) {
                if (SettingManager::get('setting.control.convocationContent.name')) {
                    $docName = '@LuccaMinute/Control/Printing/Custom/convocation_content-' .
                        SettingManager::get('setting.general.departement.name') . '.html.twig';
                } else {
                    $docName = '@LuccaMinute/Control/Printing/Basic/convocation_content.html.twig';
                }
                $edition->setLetterConvocation(
                    $this->renderView($docName, array(
                        'minute' => $minute,
                        'adherent' => $minute->getAdherent(),
                        'control' => $control,
                        'human' => $edition->getHuman(),
                        'officialLogo' => $logo,
                    ))
                );
            }
        }

        $form = $this->createForm(ControlConvocationType::class, $control, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean letter content if boolean is false */
            foreach ($control->getEditions() as $edition) {
                if (!$edition->getConvocationEdited())
                    $edition->setLetterConvocation(null);
                else {
                    /** Call service to clean all html of this step from useless fonts */
                    $edition->setLetterConvocation($this->htmlCleaner->removeAllFonts($edition->getLetterConvocation()));
                }
            }

            $em->persist($control);
            $em->flush();

            $this->addFlash('success', 'flash.control.convocation.updatedSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('@LuccaMinute/ControlEdition/letterConvocation.html.twig', array(
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'control' => $control,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ));
    }
}
