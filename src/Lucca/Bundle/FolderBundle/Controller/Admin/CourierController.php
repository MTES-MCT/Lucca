<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Controller\Admin;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Doctrine\ORM\ORMException;
use Lucca\MinuteBundle\Form\CourierType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CourierController
 *
 * @Route("/minute-{minute_id}/courier")
 * @Security("has_role('ROLE_LUCCA')")
 * @ParamConverter("minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierController extends Controller
{
    /*************************** Judicial ***************************/

    /**
     * Judicial Date
     *
     * @Route("-{id}/judicial-date", name="lucca_courier_judicial_date", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Courier $courier
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    public function judicialDateAction(Request $request, Minute $minute, Courier $courier)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(CourierType::class, $courier, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean html from useless font and empty span */
            $courier->setContext($this->get('lucca.utils.html_cleaner')->removeAllFonts($courier->getContext()));

            $em->persist($courier);
            $em->flush();

            $this->addFlash('success', 'flash.courier.judicialUpdate');

            /** update status of the minute */
            $this->get('lucca.manager.minute_story')->manage($minute);
            $em->flush();

            if ($request->request->get('saveAndContinue') !== null)
                return $this->redirectToRoute('lucca_courier_manual_judicial', array(
                    'minute_id' => $minute->getId(), 'id' => $courier->getId()
                ));

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('LuccaMinuteBundle:Courier:judicial.html.twig', array(
            'minute' => $minute,
            'courier' => $courier,
            'form' => $form->createView(),
        ));
    }
}
