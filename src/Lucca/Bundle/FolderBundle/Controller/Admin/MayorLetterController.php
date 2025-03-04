<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\FolderBundle\Entity\MayorLetter;
use Lucca\Bundle\FolderBundle\Form\MayorLetterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_LUCCA')]
#[Route(path: '/mayor-letter')]
class MayorLetterController extends AbstractController
{
    /**
     * Generate Mayor Letter
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route(path: '/step-1', name: 'lucca_mayor_letter_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(Request $request): RedirectResponse|Response
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

        return $this->render('@LuccaFolder/MayorLetter/edit.html.twig', array(
            'form' => $form->createView(),
            'adherent' => $adherent,
        ));
    }
}
