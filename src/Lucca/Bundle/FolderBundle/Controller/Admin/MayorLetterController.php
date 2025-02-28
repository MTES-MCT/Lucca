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

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\MayorLetter;
use Lucca\MinuteBundle\Form\MayorLetterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MayorLetterController
 *
 * @Route("/mayor-letter")
 * @Security("has_role('ROLE_LUCCA')")
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Lisa <lisa.alvarez@numeric-wave.eu>
 */
class MayorLetterController extends Controller
{
    /**
     * Generate Mayor Letter
     *
     * @Route("/step-1", name="lucca_mayor_letter_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
        /** Who is connected ;) */
        $adherent = $this->get('lucca.finder.adherent')->whoAmI();

        $mayorLetter = new MayorLetter();
        $form = $this->createForm(MayorLetterType::class, $mayorLetter, array(
            'adherent' => $adherent
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($request->get('print') !== null) {

                $em = $this->getDoctrine()->getManager();

                /* get invoices from invoiceIds */
                $foldersIds = json_decode($form->get('folders')->getData(), true);
                $folders = $em->getRepository(Folder::class)->findByIds($foldersIds);

                /* set Invoices in Dunning */
                $mayorLetter->setFolders($folders);
                /* set Adherent */
                $mayorLetter->setAdherent($adherent);

                $em->persist($mayorLetter);
                $em->flush();

                /** redirect to print route */
                return $this->redirectToRoute('lucca_mayor_letter_print', array('id' => $mayorLetter->getId()));
            }
        }

        return $this->render('LuccaMinuteBundle:MayorLetter:edit.html.twig', array(
            'form' => $form->createView(),
            'adherent' => $adherent,
        ));
    }
}