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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\ModelBundle\Manager\ModelManager;
use Lucca\Bundle\ModelBundle\Entity\Model;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;

#[Route(path: '/model')]
#[IsGranted('ROLE_USER')]
class ModelAdvancedController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdherentFinder $adherentFinder,
        private readonly ModelManager $modelManager,
    )
    {
    }

    /**
     * Duplicate a Model
     */
    #[Route(path: '/duplicate-{id}', name: 'lucca_model_duplicate', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function duplicateAction(Model $model): Response
    {
        $adherent = $this->adherentFinder->whoAmI();

        /** Check if the user can access to the edition of model */
        if ($adherent->getFunction() !== Adherent::FUNCTION_COUNTRY_AGENT and $model->getType() === Model::TYPE_ORIGIN) {
            $this->addFlash('danger', 'flash.model.accessDenied');

            return $this->redirectToRoute('lucca_model_index');
        }

        $newModel = $this->modelManager->duplicate($model);
        $newModel = $this->modelManager->manage($newModel, $adherent);

        $this->em->persist($newModel);
        $this->em->flush();

        return $this->redirectToRoute('lucca_model_edit', array('id' => $newModel->getId()));
    }
}
