<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\FolderBundle\Entity\MayorLetter;
use Lucca\Bundle\FolderBundle\Form\MayorLetterType;

#[IsGranted('ROLE_LUCCA')]
#[Route(path: '/mayor-letter')]
class MayorLetterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdherentFinder $adherentFinder,
    )
    {
    }

    /**
     * Generate Mayor Letter
     */
    #[Route(path: '/step-1', name: 'lucca_mayor_letter_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(Request $request): Response
    {
        /** Who is connected */
        $adherent = $this->adherentFinder->whoAmI();

        $mayorLetter = new MayorLetter();
        $form = $this->createForm(MayorLetterType::class, $mayorLetter, ['adherent' => $adherent]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->get('print') !== null) {
                /* get invoices from invoiceIds */
                $foldersIds = json_decode($form->get('folders')->getData(), true);
                $folders = $this->em->getRepository(Folder::class)->findByIds($foldersIds);

                /* set Invoices in Dunning */
                $mayorLetter->setFolders($folders);
                /* set Adherent */
                $mayorLetter->setAdherent($adherent);

                $this->em->persist($mayorLetter);
                $this->em->flush();

                /** redirect to print route */
                return $this->redirectToRoute('lucca_mayor_letter_print', ['id' => $mayorLetter->getId()]);
            }
        }

        return $this->render('@LuccaFolder/MayorLetter/edit.html.twig', [
            'form' => $form->createView(),
            'adherent' => $adherent,
        ]);
    }
}
