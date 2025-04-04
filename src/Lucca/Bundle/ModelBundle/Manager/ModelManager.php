<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\ModelBundle\Entity\{Model, Page};

readonly class ModelManager
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    /**
     * Init Pages for recto and verso for the model
     */
    public function initPage(Model $model): Model
    {
        //init recto
        $recto = new Page();
        $model->setRecto($recto);

        $this->em->persist($recto);

        //init verso
        if ($model->getLayout() == Model::LAYOUT_COVER) {
            $verso = new Page();
            $model->setVerso($verso);

            $this->em->persist($verso);
        }

        return $model;
    }

    /**
     * Update Pages if layout change
     */
    public function updatePages(Model $model): Model
    {

        // update verso
        if ($model->getLayout() == Model::LAYOUT_COVER) {
            if ( $model->getVerso() === null ) {
                $verso = new Page();
                $model->setVerso($verso);

                $this->em->persist($verso);
            }
        }

        return $model;
    }

    /**
     * Manage owner of a model
     */
    public function manage(Model $model, Adherent $adherent): Model
    {
        if ($model->getType() == Model::TYPE_PRIVATE and
            ($adherent->getFunction() != Adherent::FUNCTION_COUNTRY_AGENT or $model->getOwner() == null)) {
            $model->setOwner($adherent);
        } elseif($model->getType() == Model::TYPE_ORIGIN) {
            $model->setOwner(null);
            $model->setSharedService(null);
            $model->setSharedIntercommunal(null);
            $model->setSharedTown(null);
        }

        return $model;
    }

    /**
     * Duplicate owner of a model
     */
    public function duplicate(Model $model): Model
    {
        $newModel = clone $model;
        $newModel->setName($model->getName() . ' (Copy)');
        $newModel->setId(null);

        return $newModel;
    }
}
