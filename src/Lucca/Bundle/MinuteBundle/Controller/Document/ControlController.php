<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Document;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Lucca\SettingBundle\Utils\SettingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ControlController
 *
 * @Route("/minute-{minute_id}/control-")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Controller\Admin\Doc
 * @author Terence <terence@numeric-wave.tech>
 */
class ControlController extends Controller
{
    /** Setting if use agent of refresh or minute agent */
    private $useRefreshAgentForRefreshSignature;

    public function __construct()
    {
        $this->useRefreshAgentForRefreshSignature = SettingManager::get('setting.folder.useRefreshAgentForRefreshSignature.name');
    }


    /*************************** Convocation ***************************/

    /**
     * Displays an Access letter
     *
     * @Route("{id}/letter-access", name="lucca_control_access", methods={"GET"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @param Control $control
     * @return Response
     */
    public function accessLetterAction(Minute $minute, Control $control)
    {
        $em = $this->getDoctrine()->getManager();

        $controlEdition = $em->getRepository('LuccaMinuteBundle:ControlEdition')->findOneBy(
            array('control' => $control)
        );

        if ($this->useRefreshAgentForRefreshSignature)
            $agent = $control->getAgent();
        else
            $agent = $minute->getAgent();

        return $this->render('LuccaMinuteBundle:Control:access.html.twig', array(
            'agent' => $agent,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'control' => $control,
            'controlEdition' => $controlEdition,
            'officialLogo' => $this->get('lucca.finder.logo')->findLogo($minute->getAdherent())
        ));
    }

    /*************************** Convocation ***************************/

    /**
     * Displays a Convocation letter
     *
     * @Route("{id}/letter-convocation", name="lucca_control_letter", methods={"GET"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @param Control $control
     * @return Response
     */
    public function convocationLetterAction(Minute $minute, Control $control)
    {
        $em = $this->getDoctrine()->getManager();

        $controlEdition = $em->getRepository('LuccaMinuteBundle:ControlEdition')->findOneBy(
            array('control' => $control)
        );

        return $this->render('LuccaMinuteBundle:Control:convocation.html.twig', array(
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'control' => $control,
            'controlEdition' => $controlEdition,
            'officialLogo' => $this->get('lucca.finder.logo')->findLogo($minute->getAdherent())
        ));
    }
}
