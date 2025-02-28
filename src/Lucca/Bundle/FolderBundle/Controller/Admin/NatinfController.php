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

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Natinf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NatinfController
 *
 * @Route("/natinf")
 * @Security("has_role('ROLE_USER')")
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class NatinfController extends Controller
{
    /**
     * List of Natinf
     *
     * @Route("/", name="lucca_natinf_index", methods={"GET"})
     * @Security("has_role('ROLE_USER')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $natinfs = $em->getRepository('LuccaMinuteBundle:Natinf')->findAll();

        return $this->render('LuccaMinuteBundle:Natinf:index.html.twig', array(
            'natinfs' => $natinfs
        ));
    }

    /**
     * Creates a new Natinf entity.
     *
     * @Route("/new", name="lucca_natinf_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $natinf = new Natinf();

        $form = $this->createForm('Lucca\MinuteBundle\Form\NatinfType', $natinf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($natinf);
            $em->flush();

            $this->addFlash('success', 'flash.natinf.createdSuccessfully');
            return $this->redirectToRoute('lucca_natinf_show', array('id' => $natinf->getId()));
        }

        return $this->render('LuccaMinuteBundle:Natinf:new.html.twig', array(
            'natinf' => $natinf,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Natinf entity.
     *
     * @Route("/{id}", name="lucca_natinf_show", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Natinf $natinf
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Natinf $natinf)
    {
        $deleteForm = $this->createDeleteForm($natinf);

        return $this->render('LuccaMinuteBundle:Natinf:show.html.twig', array(
            'natinf' => $natinf,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Natinf entity.
     *
     * @Route("/{id}/edit", name="lucca_natinf_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Natinf $natinf
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Natinf $natinf)
    {
        $deleteForm = $this->createDeleteForm($natinf);
        $editForm = $this->createForm('Lucca\MinuteBundle\Form\NatinfType', $natinf);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($natinf);
            $em->flush();

            $this->addFlash('info', 'flash.natinf.updatedSuccessfully');
            return $this->redirectToRoute('lucca_natinf_show', array('id' => $natinf->getId()));
        }

        return $this->render('LuccaMinuteBundle:Natinf:edit.html.twig', array(
            'natinf' => $natinf,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Natinf entity.
     *
     * @Route("/{id}", name="lucca_natinf_delete", methods={"DELETE"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Natinf $natinf
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Natinf $natinf)
    {
        $form = $this->createDeleteForm($natinf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($natinf);
            $em->flush();
        }

        $this->addFlash('danger', 'flash.natinf.deletedSuccessfully');
        return $this->redirectToRoute('lucca_natinf_index');
    }

    /**
     * Creates a form to delete a Natinf entity.
     *
     * @param Natinf $natinf
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Natinf $natinf)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_natinf_delete', array('id' => $natinf->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Natinf entity.
     *
     * @Route("/{id}/enable", name="lucca_natinf_enable", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Natinf $natinf
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function enableAction(Natinf $natinf)
    {
        $em = $this->getDoctrine()->getManager();

        if ($natinf->isEnabled()) {
            $natinf->setEnabled(false);
            $this->addFlash('info', 'flash.natinf.enabledSuccessfully');
        } else {
            $natinf->setEnabled(true);
            $this->addFlash('info', 'flash.natinf.disabledSuccessfully');
        }

        $em->flush();

        return $this->redirectToRoute('lucca_natinf_index');
    }
}
