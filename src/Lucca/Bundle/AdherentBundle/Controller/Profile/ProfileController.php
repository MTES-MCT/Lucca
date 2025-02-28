<?php

namespace Lucca\AdherentBundle\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Routing\Annotation\Route;

use Lucca\AdherentBundle\Form\ProfileType;

/**
 * Class ProfileController
 *
 * @Security("has_role('ROLE_USER')")
 * @Route("/my-profile")
 *
 * @package Lucca\AdherentBundle\Controller\Profile
 * @author Terence <terence@numeric-wave.tech>
 */
class ProfileController extends Controller
{
    /**
     * Finds and displays a Adherent entity.
     *
     * @Route("/", name="lucca_adherent_profile_show", methods={"GET"})
     * @Security("has_role('ROLE_USER')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showProfileAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** Find Adherent by connected User */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $adherent = $em->getRepository('LuccaAdherentBundle:Adherent')->findOneBy(array(
            'user' => $user
        ));

        return $this->render('LuccaAdherentBundle:Profile:show.html.twig', array(
            'adherent' => $adherent,
        ));
    }

    /**
     * Displays a form to edit an existing Adherent entity.
     *
     * @Route("/edit", name="lucca_adherent_profile_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** Find Adherent by connected User */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $adherent = $em->getRepository('LuccaAdherentBundle:Adherent')->findOneBy(array(
            'user' => $user
        ));

        $editForm = $this->createForm(ProfileType::class, $adherent);

        /** Init unmapped field - email */
        $editForm->get('email')->setData($adherent->getUser()->getEmail());

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /** If user with this email already exist */
            $userExisting = $em->getRepository('LuccaUserBundle:User')->findOneBy(array(
                'email' => $editForm->get('email')->getData()
            ));
            if ($userExisting and $userExisting !== $adherent->getUser()) {
                $this->addFlash('warning', 'flash.adherent.userAlreadyExist');
                return $this->render('LuccaAdherentBundle:Profile:edit.html.twig', array(
                    'adherent' => $adherent,
                    'edit_form' => $editForm->createView(),
                ));
            }

            /** If no other user exist */
            $userManager = $this->get('fos_user.user_manager');

            $user = $adherent->getUser();
            $user->setName($adherent->getName());
            $user->setEmail($editForm->get('email')->getData());

            $userManager->updateUser($user, false);
            $em->persist($adherent);
            $em->flush();

            $this->addFlash('info', 'flash.adherent.updatedSuccessfully');
            return $this->redirectToRoute('lucca_adherent_profile_show');
        }

        return $this->render('LuccaAdherentBundle:Profile:edit.html.twig', array(
            'adherent' => $adherent,
            'edit_form' => $editForm->createView(),
        ));
    }
}
