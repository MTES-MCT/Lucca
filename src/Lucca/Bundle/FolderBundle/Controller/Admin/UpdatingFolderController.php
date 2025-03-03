<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\Admin;

use Lucca\Bundle\MinuteBundle\Entity\Updating;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Doctrine\ORM\ORMException;
use Lucca\Bundle\FolderBundle\Entity\Courier;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\FolderBundle\Form\FolderType;
use Lucca\Bundle\FolderBundle\Form\UpdatingFolderType;
/**
 * Class UpdatingFolderController
 *
 * @package Lucca\Bundle\FolderBundle\Controller\Admin
 * @author Terence <terence@numeric-wave.tech>
 */
#[IsGranted('ROLE_LUCCA')]
#[Route('/minute-{minute_id}/updating-{updating_id}/folder')]
class UpdatingFolderController extends AbstractController
{
    /**
     * Creates a new Folder entity.
     *
     * @param Minute $minute
     * @param Updating $updating
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws ORMException
     */
    #[Route('/new', name: 'lucca_updating_folder_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'updating_id')] Updating $updating,
        Request $request
    ): RedirectResponse|Response {
        $folder = new Folder();

        $folder->setMinute($minute);
        $folder->setType(Folder::TYPE_REFRESH);

        /** Add all default element to checklist */
        $folder = $this->get('lucca.manager.folder')->addChecklistToFolder($folder);

        /** Create form */
        $form = $this->createForm(UpdatingFolderType::class, $folder, array(
            'minute' => $minute, 'updating' => $updating,
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** If there is no control linked to the folder do nothing and display an error message */
            if (!$folder->getControl()){
                $this->addFlash('danger', 'flash.control.needed');
            } else {
                $em = $this->getDoctrine()->getManager();

                /** Create folder num */
                $folder->setNum($this->get('lucca.generator.folder_num')->generate($folder));

                /** Create / update / delete editions if needed */
                $this->get('lucca.manager.folder_edition')->manageEditionsOnFormSubmission($folder);

                /** update status of the minute */
                $this->get('lucca.manager.minute_story')->manage($minute);

                /** Persist folder */
                $em->persist($folder);
                $em->flush();

                $this->addFlash('success', 'flash.folder.createdSuccessfully');
                return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
            }
        }

        return $this->render('@LuccaFolder/Folder/new.html.twig', array(
            'updating' => $updating,
            'folder' => $folder,
            'minute' => $minute,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Folder entity.
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse|Response
     */
    #[Route('-{id}/edit', name: 'lucca_updating_folder_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'id')] Folder $folder
    ): RedirectResponse|Response {
        $editForm = $this->createForm(FolderType::class, $folder, array(
            'minute' => $minute,
        ));

        $editForm->remove('control');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /** Create / update / delete editions if needed */
            $this->get('lucca.manager.folder_edition')->manageEditionsOnFormSubmission($folder);

            $em->persist($folder);
            $em->flush();

            $this->get('lucca.manager.minute_story')->manage($minute);
            $em->flush();

            $this->addFlash('info', 'flash.folder.updatedSuccessfully');
            return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
        }

        return $this->render('@LuccaFolder/Folder/edit.html.twig', array(
            'folder' => $folder,
            'minute' => $minute,
            'adherent' => $minute->getAdherent(),
            'user' => $minute->getAdherent()->getUser(),
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Folder entity.
     *
     * @param Request $request
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     */
    #[Route('-{id}/delete', name: 'lucca_updating_folder_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_LUCCA')]
    public function deleteAction(
        Request $request,
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'id')] Folder $folder
    ): RedirectResponse {
        $em = $this->getDoctrine()->getManager();

        $courier = $em->getRepository('LuccaFolderBundle:Courier')->findOneBy(array(
            'folder' => $folder
        ));
        foreach ($courier->getHumansEditions() as $edition)
            $em->remove($edition);

        $em->remove($courier->getEdition());

        $em->remove($folder);
        $em->flush();

        $this->addFlash('danger', 'flash.folder.deletedSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }

    /**
     * Creates a form to delete a Folder entity.
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return FormInterface
     */
    private function createDeleteForm(Minute $minute, Folder $folder)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_updating_folder_delete', array('minute_id' => $minute->getId(), 'id' => $folder->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Close a Folder entity.
     *
     * @param Minute $minute
     * @param Folder $folder
     * @return RedirectResponse
     */
    #[Route('-{id}/close', name: 'lucca_updating_folder_fence', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function fenceAction(
        #[MapEntity(id: 'minute_id')] Minute $minute,
        #[MapEntity(id: 'id')] Folder $folder
    ): RedirectResponse {
        $em = $this->getDoctrine()->getManager();

        $courier = $em->getRepository('LuccaFolderBundle:Courier')->findOneBy(array(
            'folder' => $folder
        ));

        if (!$courier) {
            $courier = new Courier();
            $courier->setFolder($folder);

            /** Create / update / delete editions if needed */
            $this->get('lucca.manager.courier_edition')->manageEditionsOnFormSubmission($courier);

            $em->persist($courier);
            $this->addFlash('info', 'flash.courier.addSuccessfully');
        }

        $em->persist($folder);
        $em->flush();

        $this->addFlash('danger', 'flash.folder.closeSuccessfully');
        return $this->redirectToRoute('lucca_minute_show', array('id' => $minute->getId()));
    }
}
