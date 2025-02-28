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
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Minute;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Updating;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Lucca\MinuteBundle\Form\UpdatingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UpdatingController
 *
 * @Security("has_role('ROLE_LUCCA')")
 * @Route("/minute-{minute_id}/updating")
 * @ParamConverter("minute", class="LuccaMinuteBundle:Minute", options={"id" = "minute_id"})
 *
 * @package Lucca\MinuteBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
class UpdatingController extends Controller
{
    /**
     * Creates a new Folder entity.
     *
     * @Route("/new", name="lucca_updating_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @return RedirectResponse
     * @throws NonUniqueResultException|ORMException
     */
    public function newAction(Minute $minute)
    {
        $em = $this->getDoctrine()->getManager();

        $updating = new Updating();
        $updating->setMinute($minute);
        $updating->setNum($this->get('lucca.generator.updating_num')->generate($updating));

        /** update status of the minute */
        $this->get('lucca.manager.minute_story')->manage($minute);

        $this->addFlash('success', 'flash.updating.createdSuccessfully');
        $em->persist($updating);
        $em->flush();

        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'updating-' . $updating->getId()));
    }

    /**
     * Edit or create Step 1 - Folder
     *
     * @Route("-{id}/step-1", name="lucca_updating_step1")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Minute $minute
     * @param Updating $updating
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function step1Action(Request $request, Minute $minute, Updating $updating)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(UpdatingType::class, $updating, array());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Clean html from useless font and empty span */
            $updating->setDescription($this->get('lucca.utils.html_cleaner')->removeAllFonts($updating->getDescription()));

            $em->persist($updating);
            $em->flush();

            $this->addFlash('success', 'flash.updating.updatedSuccessfully');

            if ($request->request->get('saveAndContinue') !== null)
                return $this->redirectToRoute('lucca_updating_folder_new', array(
                    'minute_id' => $updating->getMinute()->getId(), 'updating_id' => $updating->getId()
                ));

            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId(), '_fragment' => 'updating-' . $updating->getId()));
        }

        return $this->render('LuccaMinuteBundle:Updating:step1.html.twig', array(
            'updating' => $updating,
            'minute' => $minute,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Folder entity.
     *
     * @Route("-{id}/delete", name="lucca_updating_delete")
     * @Security("has_role('ROLE_LUCCA')")
     *
     * @param Request $request
     * @param Minute $minute
     * @param Updating $updating
     * @return RedirectResponse
     * @throws ORMException
     */
    public function deleteAction(Request $request, Minute $minute, Updating $updating)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($updating);
        $em->flush();

        $this->get('lucca.manager.minute_story')->manage($minute);
        $em->flush();

        $this->addFlash('danger', 'flash.updating.deletedSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }

    /**
     * Creates a form to delete a Folder entity.
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Minute $minute, Folder $folder)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_updating_delete', array('minute_id' => $minute->getId(), 'id' => $folder->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
