<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Controller\ByMargin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\ModelBundle\Entity\{Margin, Model, Page};
use Lucca\Bundle\ModelBundle\Form\MarginBlocsType;

#[Route(path: '/model-{mod_id}/page-{p_id}/margin-{mar_id}/bloc')]
#[IsGranted('ROLE_USER')]
class BlocController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdherentFinder         $adherentFinder,
    )
    {
    }

    /**
     * Edit a new Margin entity.
     */
    #[Route(path: '/edit', name: 'lucca_model_bloc_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(
        #[MapEntity(id: 'p_id')] Page     $page,
        #[MapEntity(id: 'mod_id')] Model  $model,
        #[MapEntity(id: 'mar_id')] Margin $margin,
        Request                           $request,
    ): Response
    {
        /** Who is connected */
        $adherent = $this->adherentFinder->whoAmI();

        /** Check if the user can access to the edition of model */
        if ($adherent->getFunction() !== Adherent::FUNCTION_COUNTRY_AGENT && $model->getType() === Model::TYPE_ORIGIN) {
            $this->addFlash('danger', 'flash.model.accessDenied');

            return $this->redirectToRoute('lucca_model_index');
        }

        $editForm = $this->createForm(MarginBlocsType::class, $margin);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em->persist($margin);
            $this->em->flush();

            $this->addFlash('success', 'flashes.created_successfully');

            return $this->redirectToRoute('lucca_model_show', ['id' => $model->getId()]);
        }

        return $this->render('@LuccaModel/Margin/edit.html.twig', [
            'page' => $page,
            'model' => $model,
            'margin' => $margin,
            'edit_form' => $editForm->createView(),
        ]);
    }
}
