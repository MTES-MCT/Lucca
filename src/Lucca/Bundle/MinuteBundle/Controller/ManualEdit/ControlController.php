<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\ManualEdit;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\ControlEdition;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Lucca\MinuteBundle\Form\Control\ControlAccessType;
use Lucca\MinuteBundle\Form\Control\ControlConvocationType;
use Lucca\SettingBundle\Utils\SettingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ControlController
 *
 * @Route("/minute-{minute_id}/control-")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Controller\ManualEdit
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class ControlController extends Controller
{
    /** Setting if use agent of refresh or minute agent */
    private $useRefreshAgentForRefreshSignature;

    public function __construct()
    {
        $this->useRefreshAgentForRefreshSignature = SettingManager::get('setting.folder.useRefreshAgentForRefreshSignature.name');
    }


    /**
     * Displays an Access letter
     *
     * @Route("{id}/letter-access/edit-manually", name="lucca_control_access_manual", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Control $control
     * @return RedirectResponse|Response
     */
    public function accessLetterAction(Request $request, Minute $minute, Control $control)
    {
        $em = $this->getDoctrine()->getManager();

        /** If number of editions is different to nuymber of human - recreate editions */
        if ($control->getEditions()->count() !== ($control->getHumansByControl()->count() + $control->getHumansByMinute()->count())) {
            $this->get('lucca.manager.control_edition')->manageEditionsOnFormSubmission($control);

            $this->addFlash('success', 'flash.control.updatedSuccessfully');
            $em->flush();
        }

        if ($this->useRefreshAgentForRefreshSignature)
            $agent = $control->getAgent();
        else
            $agent = $minute->getAgent();

        $logo = $this->get('lucca.utils.printer.control')->defineLogo($minute->getAdherent());

        /** Fill all empty editions with basic view */
        foreach ($control->getEditions() as $edition) {
            $docName = null;

            if (!$edition->getAccessEdited() && $control->getStateControl() === Control::STATE_NEIGHBOUR) {
                if (SettingManager::get('setting.control.accessEmpty.name')) {
                    $docName = 'LuccaMinuteBundle:Control/Printing/Custom:access_empty-' .
                        SettingManager::get('setting.general.departement.name') . '.html.twig';
                } else {
                    $docName = 'LuccaMinuteBundle:Control/Printing/Basic:access_empty.html.twig';
                }
            } elseif (!$edition->getAccessEdited()) {
                if (SettingManager::get('setting.control.accessContent.name')) {
                    $docName = 'LuccaMinuteBundle:Control/Printing/Custom:access_content-' .
                        SettingManager::get('setting.general.departement.name') . '.html.twig';
                } else {
                    $docName = 'LuccaMinuteBundle:Control/Printing/Basic:access_content.html.twig';
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
                    $edition->setLetterAccess($this->get('lucca.utils.html_cleaner')->removeAllFonts($edition->getLetterAccess()));
                }
            }
            $em->persist($control);
            $em->flush();

            $this->addFlash('success', 'flash.control.access.updatedSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }


        return $this->render('LuccaMinuteBundle:ControlEdition:letterAccess.html.twig', array(
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'control' => $control,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays an Access letter
     *
     * @Route("{id}/letter-convocation/edit-manually", name="lucca_control_convocation_manual", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Control $control
     * @return RedirectResponse|Response
     */
    public function convocationLetterAction(Request $request, Minute $minute, Control $control)
    {
        $em = $this->getDoctrine()->getManager();

        /** If number of editions is different to nuymber of human - recreate editions */
        if ($control->getEditions()->count() !== ($control->getHumansByControl()->count() + $control->getHumansByMinute()->count())) {
            $this->get('lucca.manager.control_edition')->manageEditionsOnFormSubmission($control);

            $this->addFlash('success', 'flash.control.updatedSuccessfully');
            $em->flush();
        }
        $logo = $this->get('lucca.utils.printer.control')->defineLogo($minute->getAdherent());

        /** Fill all empty editions with basic view */
        /** @var ControlEdition $edition */
        foreach ($control->getEditions() as $edition) {
            if (!$edition->getConvocationEdited()) {
                if (SettingManager::get('setting.control.convocationContent.name')) {
                    $docName = 'LuccaMinuteBundle:Control/Printing/Custom:convocation_content-' .
                        SettingManager::get('setting.general.departement.name') . '.html.twig';
                } else {
                    $docName = 'LuccaMinuteBundle:Control/Printing/Basic:convocation_content.html.twig';
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
                    $edition->setLetterConvocation($this->get('lucca.utils.html_cleaner')->removeAllFonts($edition->getLetterConvocation()));
                }
            }

            $em->persist($control);
            $em->flush();

            $this->addFlash('success', 'flash.control.convocation.updatedSuccessfully');

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('LuccaMinuteBundle:ControlEdition:letterConvocation.html.twig', array(
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'control' => $control,
            'officialLogo' => $logo,
            'form' => $form->createView(),
        ));
    }
}
