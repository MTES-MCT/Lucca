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

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagController
 *
 * @Route("/tag")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class TagController extends Controller
{
    /**
     * List of Tag
     *
     * @Route("/", name="lucca_tag_index", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tags = $em->getRepository('LuccaMinuteBundle:Tag')->findAll();

        return $this->render('LuccaMinuteBundle:Tag:index.html.twig', array(
            'tags' => $tags
        ));
    }

    /**
     * Creates a new Tag entity.
     *
     * @Route("/new", name="lucca_tag_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $tag = new Tag();

        $form = $this->createForm('Lucca\MinuteBundle\Form\TagType', $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($tag->getProposals() as $proposal) {
                $tag->addProposal($proposal);
            }

            $em->persist($tag);
            $em->flush();

            $this->addFlash('success', 'flash.tag.createdSuccessfully');
            return $this->redirectToRoute('lucca_tag_show', array('id' => $tag->getId()));
        }

        return $this->render('LuccaMinuteBundle:Tag:new.html.twig', array(
            'tag' => $tag,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tag entity.
     *
     * @Route("/{id}", name="lucca_tag_show", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Tag $tag
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Tag $tag)
    {
        $deleteForm = $this->createDeleteForm($tag);

        return $this->render('LuccaMinuteBundle:Tag:show.html.twig', array(
            'tag' => $tag,
            'proposals' => $tag->getProposals(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tag entity.
     *
     * @Route("/{id}/edit", name="lucca_tag_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Tag $tag
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Tag $tag)
    {
        $deleteForm = $this->createDeleteForm($tag);
        $editForm = $this->createForm('Lucca\MinuteBundle\Form\TagType', $tag);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($tag->getProposals() as $proposal) {
                $tag->addProposal($proposal);
            }

            $em->persist($tag);
            $em->flush();

            $this->addFlash('info', 'flash.tag.updatedSuccessfully');
            return $this->redirectToRoute('lucca_tag_show', array('id' => $tag->getId()));
        }

        return $this->render('LuccaMinuteBundle:Tag:edit.html.twig', array(
            'tag' => $tag,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Tag entity.
     *
     * @Route("/{id}", name="lucca_tag_delete", methods={"DELETE"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Tag $tag
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Tag $tag)
    {
        $form = $this->createDeleteForm($tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($tag);
            $em->flush();
        }

        $this->addFlash('danger', 'flash.tag.deletedSuccessfully');
        return $this->redirectToRoute('lucca_tag_index');
    }

    /**
     * Creates a form to delete a Tag entity.
     *
     * @param Tag $tag
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Tag $tag)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_tag_delete', array('id' => $tag->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Tag entity.
     *
     * @Route("/{id}/enable", name="lucca_tag_enable", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Tag $tag
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function enableAction(Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();

        if ($tag->getEnabled()) {
            $tag->setEnabled(false);
            $this->addFlash('info', 'flash.tag.disabledSuccessfully');
        } else {
            $tag->setEnabled(true);
            $this->addFlash('info', 'flash.tag.enabledSuccessfully');
        }
        $em->flush();

        return $this->redirectToRoute('lucca_tag_index');
    }
}
