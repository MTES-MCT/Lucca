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

namespace Lucca\MinuteBundle\Controller\Admin;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Closure;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Doctrine\ORM\ORMException;
use Lucca\MinuteBundle\Form\ClosureType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClosureController
 *
 * @Security("has_role('ROLE_LUCCA')")
 * @Route("/minute")
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class ClosureController extends Controller
{
    /**
     * Close a Minute entity.
     *
     * @Route("-{id}/close", name="lucca_minute_close", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    public function closeAction(Request $request, Minute $minute)
    {
        $closure = new Closure();
        $closure->setMinute($minute);

        $form = $this->createForm(ClosureType::class, $closure, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $this->get('lucca.manager.closure')->closeMinute($minute);

            /** Call service to clean all html of this step from useless fonts */
            $closure->setObservation($this->get('lucca.utils.html_cleaner')->removeAllFonts($closure->getObservation()));

            $em->persist($closure);
            $em->persist($minute);
            $em->flush();

            /** update status of the minute */
            $this->get('lucca.manager.minute_story')->manage($minute);
            $em->flush();

            $this->addFlash('info', 'flash.closure.closedSuccessfully');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'closure-' . $closure->getId()));
        }

        return $this->render('LuccaMinuteBundle:Closure:close.html.twig', array(
            'minute' => $minute,
            'closure' => $closure,
            'form' => $form->createView(),
        ));
    }

    /**
     * Open a Minute entity.
     *
     * @Route("-{id}/open", name="lucca_minute_open", methods={"GET", "POST"})
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param Minute $minute
     * @return RedirectResponse
     * @throws ORMException
     */
    public function openAction(Minute $minute)
    {
        $em = $this->getDoctrine()->getManager();
        $closure = $em->getRepository('LuccaMinuteBundle:Closure')->findOneBy(array(
            'minute' => $minute
        ));
        $closure->setMinuteOpen($minute);

        $em->persist($minute);

        $em->remove($closure);
        $em->flush();

        /** update status of the minute */
        $this->get('lucca.manager.minute_story')->manage($minute);
        $em->flush();

        $this->addFlash('warning', 'flash.closure.openSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }
}