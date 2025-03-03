<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Lucca\Bundle\FolderBundle\Entity\Courier;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Doctrine\ORM\ORMException;
use Lucca\Bundle\FolderBundle\Form\CourierType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class CourierController
 *
 * @package Lucca\Bundle\FolderBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
#[IsGranted('ROLE_LUCCA')]
#[Route('/minute-{minute_id}/courier')]
class CourierController extends AbstractController
{
    /*************************** Judicial ***************************/

    /**
     * Judicial Date
     *
     * @param Request $request
     * @param Minute $minute
     * @param Courier $courier
     * @return RedirectResponse|Response|null
     * @throws ORMException
     */
    #[IsGranted('ROLE_LUCCA')]
    #[Route('/minute-{minute_id}/courier')]
    public function judicialDateAction(Request $request, Minute $minute, Courier $courier): RedirectResponse|Response|null
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

        return $this->render('@LuccaFolder/Courier/judicial.html.twig', array(
            'minute' => $minute,
            'courier' => $courier,
            'form' => $form->createView(),
        ));
    }
}
