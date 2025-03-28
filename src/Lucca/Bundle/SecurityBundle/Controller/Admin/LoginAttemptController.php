<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\SecurityBundle\Entity\LoginAttempt;
use Lucca\Bundle\SecurityBundle\Form\BrowserLoginAttemptType;
use Lucca\Bundle\SecurityBundle\Manager\LoginAttemptManager;

#[Route(path: '/security/login-attempt')]
#[IsGranted('ROLE_ADMIN')]
class LoginAttemptController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoginAttemptManager $loginAttemptManager,
    )
    {
    }

    /**
     * List of LoginAttempt
     */
    #[Route(path: '/', name: 'lucca_security_login_attempt_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $form = $this->createForm(BrowserLoginAttemptType::class);

        return $this->render('@LuccaSecurity/LoginAttempt/Admin/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a LoginAttempt entity.
     */
    #[Route(path: '-{id}', name: 'lucca_security_login_attempt_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(LoginAttempt $p_loginAttempt): Response
    {
        $deleteForm = $this->createDeleteForm($p_loginAttempt);

        return $this->render('@LuccaSecurity/LoginAttempt/Admin/show.html.twig', [
            'loginAttempt' => $p_loginAttempt,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a LoginAttempt entity.
     */
    #[Route(path: '/{id}', name: 'lucca_security_login_attempt_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, LoginAttempt $loginAttempt): Response
    {
        $form = $this->createDeleteForm($loginAttempt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($loginAttempt);
            $this->em->flush();

            $this->addFlash('success', 'flash.loginAttempt.deletedSuccessfully');
        } else {
            $this->addFlash('success', 'flash.loginAttempt.deletedCancelled');
        }

        return $this->redirectToRoute('lucca_security_login_attempt_index');
    }

    /**
     * Creates a form to delete a LoginAttempt entity.
     */
    private function createDeleteForm(LoginAttempt $loginAttempt): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_security_login_attempt_delete', ['id' => $loginAttempt->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and displays a LoginAttempt entity
     */
    #[Route(path: '-{id}/approve-ip', name: 'lucca_security_login_attempt_approve_ip', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function approveIpAction(LoginAttempt $p_loginAttempt): Response
    {
        $results = $this->loginAttemptManager->approveIp($p_loginAttempt->getRequestIp());

        if (count($results) > 0) {
            $this->addFlash('success', 'flash.loginAttempt.ipClearedSuccessfully');
        }

        return $this->redirectToRoute('lucca_security_login_attempt_show', ['id' => $p_loginAttempt->getId()]);
    }
}
