<?php

/*
 * Copyright (c) 2025-2026. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Form\AdherentType;
use Lucca\Bundle\AdherentBundle\Manager\AdherentManager;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/adherent')]
#[IsGranted('ROLE_ADMIN')]
class AdvancedAdherentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdherentManager $adherentManager,
        private readonly UserDepartmentResolver $userDepartmentResolver,
    ) {
    }

    /**
     * Creates a new Adherent for an existing User (Duplication/Multi-department membership).
     */
    #[Route(path: '/{id}/duplicate', name: 'lucca_adherent_duplicate', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function duplicateAction(Request $request, Adherent $adherentSource): Response {
        $loggedUserDept = $this->userDepartmentResolver->getDepartment();
        $userSource = $adherentSource->getUser();

        // Security check: Department admins are not allowed to duplicate across departments
        if ($loggedUserDept !== null) {
            return $this->redirectToRoute('lucca_adherent_new');
        }

        // Check if there are remaining departments available for this user
        $availableDepts = $this->em->getRepository(Department::class)->findNoExistingForAdherents($userSource);
        if (empty($availableDepts)) {
            $this->addFlash('warning', 'flash.adherent.noDepartmentAvailableForDuplication');

            $referer = $request->headers->get('referer');
            return $referer ? $this->redirect($referer) : $this->redirectToRoute('lucca_adherent_index');
        }

        // Initialize by cloning only on GET requests to pre-fill the form
        $adherent = $request->isMethod('POST') ? new Adherent() : clone $adherentSource;

        $form = $this->createForm(AdherentType::class, $adherent, [
            'showDepartment' => true, // User must select the new department
            'user' => $userSource,    // Link to existing user
        ]);

        $form->handleRequest($request);

        // Handle AJAX refresh if the form is dynamic (e.g., department change updates fields)
        if ($request->isXmlHttpRequest() && $form->isSubmitted()) {
            return $this->render('@LuccaAdherent/Adherent/new.html.twig', [
                'adherent' => $adherent,
                'form' => $form->createView(),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $adherent->setUser($userSource);
            $targetDepartment = $adherent->getDepartment();

            // Validate prerequisites (passing false as it's not a brand new User account)
            if ($this->adherentManager->checkPrerequisites($adherent, $targetDepartment, false)) {
                $this->em->persist($adherent);
                $this->em->flush();

                $this->addFlash('success', 'flash.adherent.createdSuccessfully');

                return $this->redirectToRoute('lucca_adherent_show', ['id' => $adherent->getId()]);
            }
        }

        return $this->render('@LuccaAdherent/Adherent/new.html.twig', [
            'adherent' => $adherent,
            'form' => $form->createView(),
            'is_duplicate' => true
        ]);
    }
}
