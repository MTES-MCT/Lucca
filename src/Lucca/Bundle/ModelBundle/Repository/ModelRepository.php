<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Repository;

use Doctrine\ORM\{NonUniqueResultException, QueryBuilder};

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;
use Lucca\Bundle\ModelBundle\Entity\Model;
use Lucca\Bundle\ParameterBundle\Entity\{Intercommunal, Service, Town};
use Lucca\Bundle\DepartmentBundle\Entity\Department;

class ModelRepository extends LuccaRepository
{
    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Model dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryModel();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Model dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryModel();

        $qb->where($qb->expr()->eq('model.id', ':q_model'))
            ->setParameter(':q_model', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Model Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /*********************              Custom findAll methods              *******************
    /*******************************************************************************************/

    /**
     * @throws NonUniqueResultException
     */
    public function findByDocument(string $document, Adherent $p_adherent = null, Service $p_service = null,
                                   Intercommunal $p_intercommunal = null, Town $p_town = null, Department $dept = null): ?Model
    {
        $qb = $this->queryModel();

        $qb->andWhere($qb->expr()->like('model.documents', ':q_document'))
            ->setParameter(':q_document', "%$document%");

        if ($dept != null) {
            $qb->andWhere($qb->expr()->eq('model.department', ':q_department'))
                ->setParameter(':q_department', $dept);
        }

        /** If adherent is defined try to find the model he create, if there is no adherent try to find default model */
        if ($p_adherent != null) {
            $qb->andWhere($qb->expr()->eq('model.owner', ':q_owner'))
                ->setParameter(':q_owner', $p_adherent);
        } elseif($p_service != null) {
            /** If service is defined try to find the model shared with them */
            $qb->andWhere($qb->expr()->eq('model.sharedService', ':q_service'))
                ->setParameter(':q_service', $p_service);
        } elseif($p_intercommunal != null) {
            /** If interco is defined try to find the model shared with them */
            $qb->andWhere($qb->expr()->eq('model.sharedIntercommunal', ':q_intercommunal'))
                ->setParameter(':q_intercommunal', $p_intercommunal);
        } elseif($p_town != null) {
            /** If town is defined try to find the model shared with them */
            $qb->andWhere($qb->expr()->eq('model.sharedTown', ':q_town'))
                ->setParameter(':q_town', $p_town);
        } else {
            $qb->andWhere($qb->expr()->eq('model.type', ':q_type'))
                ->setParameter(':q_type', Model::TYPE_ORIGIN);
        }

        /** We want only enable model */
        $qb->andWhere($qb->expr()->eq('model.enabled', ':q_enabled'))
            ->setParameter(':q_enabled', true);

        /** Order by date creation in order to find the last created */
        $qb->orderBy('model.createdAt');

        /** Set max result to one in order to get only the last model */
        /** TODO Add a more complex logic to get only one model */
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /*******************************************************************************************/
    /*********************       Query - Dependencies of Model Entity       ********************/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryModel(): QueryBuilder
    {
        return $this->createQueryBuilder('model')
            ->leftJoin('model.recto', 'recto')->addSelect('recto')
            ->leftJoin('model.verso', 'verso')->addSelect('verso')
        ;
    }
}
