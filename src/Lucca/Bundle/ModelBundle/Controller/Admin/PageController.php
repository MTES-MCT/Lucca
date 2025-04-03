<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\ModelBundle\Manager\PageManager;
use Lucca\Bundle\ModelBundle\Entity\{Page, Model};
use Lucca\Bundle\ModelBundle\Form\PageType;

#[Route(path: '/model-{mod_id}/page')]
#[IsGranted('ROLE_LUCCA')]
class PageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdherentFinder         $adherentFinder,
        private readonly PageManager            $pageManager,
    )
    {
    }

    /**
     * Edit a new Page entity.
     */
    #[Route(path: '-{id}/edit', name: 'lucca_model_page_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LUCCA')]
    public function editAction(
        #[MapEntity(id: 'mod_id')] Model $model,
        Page $page,
        Request $request,
    ): Response
    {
        /** Who is connected */
        $adherent = $this->adherentFinder->whoAmI();

        /** Check if the user can access to the edition of model */
        if ($adherent->getFunction() !== Adherent::FUNCTION_COUNTRY_AGENT && $model->getType() === Model::TYPE_ORIGIN) {
            $this->addFlash('danger', 'flash.model.accessDenied');

            return $this->redirectToRoute('lucca_model_index');
        }

        $oldPage = clone $page;

        $editForm = $this->createForm(PageType::class, $page);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // Manage and set margin on page
            $pageManaged = $this->pageManager->manageMarginsOnPage($model, $page, $oldPage);
            if ($pageManaged != null) {
                $this->em->persist($pageManaged);
                $this->em->flush();

                $this->addFlash('success', 'flashes.created_successfully');

                return $this->redirectToRoute('lucca_model_show', ['id' => $model->getId()]);
            }
        }

        return $this->render('@LuccaModel/Page/edit.html.twig', [
            'model' => $model,
            'page' => $page,
            'edit_form' => $editForm->createView(),
        ]);
    }
}
