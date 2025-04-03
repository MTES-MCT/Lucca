<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Controller\Admin;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Form\{AdherentType, AdherentEditType};
use Lucca\Bundle\AdherentBundle\Mailer\SummaryAdherentSubscriptionMailer;
use Lucca\Bundle\AdherentBundle\Manager\AdherentManager;

#[Route(path: '/adherent')]
#[IsGranted('ROLE_ADMIN')]
class AdherentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdherentManager $adherentManager,
        private readonly SummaryAdherentSubscriptionMailer $mailer,
    )
    {
    }

    /**
     * List of Adherent
     */
    #[Route(path: '/', name: 'lucca_adherent_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $adherents = $this->em->getRepository(Adherent::class)->findAll();

        return $this->render('@LuccaAdherent/Adherent/index.html.twig', [
            'adherents' => $adherents
        ]);
    }

    /**
     * Creates a new Adherent entity.
     *
     * @throws Exception
     */
    #[Route(path: '/new', name: 'lucca_adherent_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        $adherent = new Adherent();

        $form = $this->createForm(AdherentType::class, $adherent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() &&
            $this->adherentManager->checkPrerequisites($adherent)) {
            $this->adherentManager->initNewAdherent($adherent);

            /** Send Email Subscription with new password - get the plain password filled in form */
            $this->mailer->sendSubscriptionToAdherent(
                $adherent, $form->get('user')->get('plainPassword')->getData()
            );

            $this->em->persist($adherent);
            $this->em->flush();

            $this->addFlash('success', 'flash.adherent.createdSuccessfully');

            return $this->redirectToRoute('lucca_adherent_show', array('id' => $adherent->getId()));
        }

        return $this->render('@LuccaAdherent/Adherent/new.html.twig', [
            'adherent' => $adherent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Adherent entity.
     */
    #[Route(path: '/{id}', name: 'lucca_adherent_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Adherent $adherent): Response
    {
        $deleteForm = $this->createDeleteForm($adherent);

        return $this->render('@LuccaAdherent/Adherent/show.html.twig', [
            'adherent' => $adherent,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Adherent entity.
     */
    #[Route(path: '/{id}/edit', name: 'lucca_adherent_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Adherent $adherent): Response
    {
        $editForm = $this->createForm(AdherentEditType::class, $adherent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid() &&
            $this->adherentManager->checkPrerequisites($adherent)) {
            $this->adherentManager->editAdherent($adherent);

            $this->em->persist($adherent);
            $this->em->flush();

            $this->addFlash('success', 'flash.adherent.updatedSuccessfully');

            return $this->redirectToRoute('lucca_adherent_show', ['id' => $adherent->getId()]);
        }

        return $this->render('@LuccaAdherent/Adherent/edit.html.twig', [
            'adherent' => $adherent,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Deletes a Adherent entity.
     */
    #[Route(path: '/{id}', name: 'lucca_adherent_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Adherent $adherent): Response
    {
        $form = $this->createDeleteForm($adherent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($adherent);
            $this->em->flush();
        }

        $this->addFlash('success', 'flash.adherent.deletedSuccessfully');

        return $this->redirectToRoute('lucca_adherent_index');
    }

    /**
     * Creates a form to delete a Adherent entity.
     */
    private function createDeleteForm(Adherent $adherent): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_adherent_delete', ['id' => $adherent->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Adherent entity.
     */
    #[Route(path: '/{id}/disable', name: 'lucca_adherent_disable', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route(path: '/{id}/enable', name: 'lucca_adherent_enable', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Adherent $adherent): Response
    {
        if ($adherent->getUser()->isEnabled()) {
            $adherent->setEnabled(false);
            $adherent->getUser()->setEnabled(false);
            $this->addFlash('success', 'flash.adherent.disabledSuccessfully');
        } else {
            $adherent->setEnabled(true);
            $adherent->getUser()->setEnabled(true);
            $this->addFlash('info', 'flash.adherent.enabledSuccessfully');
        }

        $this->em->persist($adherent);
        $this->em->flush();

        return $this->redirectToRoute('lucca_adherent_index');
    }
}
