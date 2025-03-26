<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\ModelBundle\Entity\Model;

readonly class ModelFinder
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    /**
     * Find a model for the request doc and adherent
     */
    public function findModel(string $doc, Adherent $adherent): ?Model
    {
        /** First try to find the model for the adherent */
        $model = $this->em->getRepository(Model::class)->findByDocument($doc, $adherent);
        /** Then if nodel is null try to find the model for the unit rattachement */
        if ($model === null) {
            if ($adherent->getService()) {
                $model = $this->em->getRepository(Model::class)->findByDocument($doc, null, $adherent->getService());
            } elseif ($adherent->getIntercommunal()) {
                $model = $this->em->getRepository(Model::class)->findByDocument($doc, null, null, $adherent->getIntercommunal());
            } elseif ($adherent->getTown()) {
                $model = $this->em->getRepository(Model::class)->findByDocument($doc, null, null, null, $adherent->getTown());
            } else {
                $model = null;
            }
        }

        /** If model is null it's mean the adherent didn't define a custom model so fin the default one */
        if ($model == null) {
            $model = $this->em->getRepository(Model::class)->findByDocument($doc);
        }

        return $model;
    }
}
