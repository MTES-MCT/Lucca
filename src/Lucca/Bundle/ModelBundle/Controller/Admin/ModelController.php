<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\ModelBundle\Entity\Model;
use Lucca\Bundle\ModelBundle\Form\ModelType;
use Lucca\Bundle\ModelBundle\Manager\ModelManager;

#[Route(path: '/model')]
#[IsGranted('ROLE_USER')]
class ModelController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdherentFinder         $adherentFinder,
        private readonly ModelManager           $modelManager,
    )
    {
    }

    /**
     * List of Model
     */
    #[Route(path: '/', name: 'lucca_model_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexAction(): Response
    {
        $models = $this->em->getRepository(Model::class)->findAll();

        return $this->render('@LuccaModel/Model/index.html.twig', [
            'models' => $models
        ]);
    }

    /**
     * Creates a new Model entity.
     */
    #[Route(path: '/new', name: 'lucca_model_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAction(Request $request): Response
    {
        /** Who is connected */
        $adherent = $this->adherentFinder->whoAmI();
        $simpleActionNeeded = false;

        /** Check if the user is a country agent in order to enable him to create a default model or not */
        if ($adherent->getFunction() != Adherent::FUNCTION_COUNTRY_AGENT) {
            /** If the user is not a country agent we need to display him simplified actions */
            $simpleActionNeeded = true;
        }

        $model = new Model();

        $form = $this->createForm(ModelType::class, $model, ['simpleActionNeeded' => $simpleActionNeeded]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $this->modelManager->initPage($model);
            $model = $this->modelManager->manage($model, $adherent);

            $this->em->persist($model);
            $this->em->flush();

            $this->addFlash('success', 'flashes.created_successfully');

            return $this->redirectToRoute('lucca_model_show', ['id' => $model->getId()]);
        }

        return $this->render('@LuccaModel/Model/new.html.twig', [
            'model' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Model entity.
     */
    #[Route(path: '/-{id}', name: 'lucca_model_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showAction(Model $model): Response
    {
        $deleteForm = $this->createDeleteForm($model);

        return $this->render('@LuccaModel/Model/show.html.twig', [
            'model' => $model,
            'recto' => $model->getRecto(),
            'verso' => $model->getVerso(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Edit a new Model entity.
     */
    #[Route(path: '-{id}/edit', name: 'lucca_model_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Request $request, Model $model): Response
    {
        /** Who is connected */
        $adherent = $this->adherentFinder->whoAmI();
        $simpleActionNeeded = false;

        /** Check if the user can access to the edition of model */
        if ($adherent->getFunction() != Adherent::FUNCTION_COUNTRY_AGENT && $model->getType() == Model::TYPE_ORIGIN) {
            $this->addFlash('danger', 'flash.model.accessDenied');

            return $this->redirectToRoute('lucca_model_index');
        } elseif ($adherent->getFunction() != Adherent::FUNCTION_COUNTRY_AGENT) {
            /** If the user can access to the model but he is not a country agent we need to display him simplified actions */
            $simpleActionNeeded = true;
        }

        $deleteForm = $this->createDeleteForm($model);
        $editForm = $this->createForm(ModelType::class, $model, ['simpleActionNeeded' => $simpleActionNeeded]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $model = $this->modelManager->updatePages($model);
            $model = $this->modelManager->manage($model, $adherent);

            $this->em->persist($model);
            $this->em->flush();

            $this->addFlash('success', 'flashes.created_successfully');

            return $this->redirectToRoute('lucca_model_show', ['id' => $model->getId()]);
        }

        return $this->render('@LuccaModel/Model/edit.html.twig', [
            'model' => $model,
            'recto' => $model->getRecto(),
            'verso' => $model->getVerso(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Model entity.
     */
    #[Route(path: '-{id}', name: 'lucca_model_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, Model $model): Response
    {
        $form = $this->createDeleteForm($model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($model);
            $this->em->flush();
        }

        $this->addFlash('danger', 'flashes.deleted_successfully');

        return $this->redirectToRoute('lucca_model_index');
    }

    /**
     * Creates a form to delete a Model entity.
     */
    private function createDeleteForm(Model $model): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lucca_model_delete', array('id' => $model->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Finds and enable / disable a Model entity.
     */
    #[Route(path: '/{id}/enable', name: 'lucca_model_enable', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enableAction(Model $model): Response
    {
        $model->toggle();

        $this->em->flush();

        $this->addFlash('info', 'flashes.toggled_successfully');

        return $this->redirectToRoute('lucca_model_show', ['id' => $model->getId()]);
    }
}
