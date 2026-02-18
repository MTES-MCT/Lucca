<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\{NonUniqueResultException, NoResultException, QueryBuilder};

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\DepartmentBundle\Service\UserDepartmentResolver;
use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\CoreBundle\Repository\Partial\DataTableTrait;

class AdherentRepository extends ServiceEntityRepository
{
    use DataTableTrait;
    public function __construct(
        ManagerRegistry                         $mr,
        private readonly UserDepartmentResolver $userDepartmentResolver,
    )
    {
        parent::__construct($mr, Adherent::class);
    }

    /*******************************************************************************************/
    /********************* Basic functions from LuccaRepository *****/
    /*******************************************************************************************/

    /**
     * Finds all enabled / disabled entities in the repository
     */
    public function findAllActive(bool $enabled = true): array
    {
        return $this->findBy([
            'enabled' => $enabled
        ]);
    }

    /**
     * Find last code use for mix prefix / year
     *
     * @throws NonUniqueResultException
     */
    public function findMaxCode($year, $prefix): mixed
    {
        $qb = $this->queryAdherent();

        $qb->andWhere($qb->expr()->like('entity.id', ':q_code'))
            ->setParameter('q_code', "%$prefix$year%");

        $qb->select($qb->expr()->max('entity.id'));

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Count all enabled / disabled entities in the repository
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countActive(bool $enabled = true): int
    {
        $qb = $this->queryAdherent();

        $qb->select($qb->expr()->count('entity'));

        $qb->andWhere($qb->expr()->eq('entity.enabled', ':q_state'))
            ->setParameter(':q_state', $enabled);

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Custom server-side processing for Adherents DataTable.
     * Handles virtual fields (officialName, location) for both global and individual column searching.
     *
     * @param array $params Raw DataTables request parameters
     * @return array Formatted results for DataTables
     */
    public function searchAdherentsForDatatable(array $params): array
    {
        // Initialize QueryBuilder with base joins
        $qb = $this->queryAdherent();
        $qb->leftJoin('adherent.logo', 'logo')->addSelect('logo');

        $rootAlias = $qb->getRootAliases()[0];

        // --- 1. CUSTOM ORDERING INTERCEPTION ---
        // Manually handle sorting for virtual or joined columns
        if (isset($params['order'][0])) {
            $colIndex = $params['order'][0]['column'];
            $colName = $params['columns'][$colIndex]['data'];
            $dir = $params['order'][0]['dir'];

            $virtualColumns = ['officialName', 'location', 'email', 'username', 'groups', 'actions'];

            if (in_array($colName, $virtualColumns)) {
                switch ($colName) {
                    case 'officialName':
                        $qb->orderBy("$rootAlias.name", $dir)->addOrderBy("$rootAlias.firstname", $dir);
                        break;
                    case 'location':
                        $qb->orderBy("town.name", $dir);
                        break;
                    case 'email':
                        $qb->orderBy("user.email", $dir);
                        break;
                    case 'username':
                        $qb->orderBy("user.username", $dir);
                        break;
                    case 'groups':
                        $qb->orderBy("groups.name", $dir);
                        break;
                    case 'actions':
                        $qb->orderBy("$rootAlias.enabled", $dir);
                        break;
                }
                // Prevent the Trait from attempting to sort again
                unset($params['order']);
            }
        }

        // --- 2. INDIVIDUAL COLUMN SEARCH INTERCEPTION ---
        // Loop through columns to catch specific searches from header inputs
        foreach ($params['columns'] as $key => $col) {
            $colSearch = $col['search']['value'] ?? null;

            if (!empty($colSearch)) {
                $colName = $col['data'];

                // Handle virtual fields specifically for individual column filtering
                if (in_array($colName, ['officialName', 'location', 'email', 'groups', 'username'])) {
                    if ($colName === 'officialName') {
                        $qb->andWhere($qb->expr()->orX(
                            $qb->expr()->like("$rootAlias.firstname", ":col_search_$key"),
                            $qb->expr()->like("$rootAlias.name", ":col_search_$key")
                        ));
                    } elseif ($colName === 'location') {
                        $qb->andWhere($qb->expr()->orX(
                            $qb->expr()->like("service.name", ":col_search_$key"),
                            $qb->expr()->like("intercommunal.name", ":col_search_$key"),
                            $qb->expr()->like("town.name", ":col_search_$key")
                        ));
                    } elseif ($colName === 'email') {
                        $qb->andWhere($qb->expr()->like("user.email", ":col_search_$key"));
                    } elseif ($colName === 'groups') {
                        $qb->andWhere($qb->expr()->like("groups.name", ":col_search_$key"));
                    } elseif ($colName === 'username') {
                        $qb->andWhere($qb->expr()->like("user.username", ":col_search_$key"));
                    }

                    $qb->setParameter("col_search_$key", "%$colSearch%");

                    // CRITICAL: Clear the search value for this column so the Trait ignores it
                    $params['columns'][$key]['search']['value'] = '';
                }
            }
        }

        // --- 3. GLOBAL SEARCH INTERCEPTION ---
        $searchValue = $params['search']['value'] ?? null;

        if (!empty($searchValue)) {
            // Apply global search across all relevant fields (including virtual/joined ones)
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like("$rootAlias.firstname", ":g_search"),
                    $qb->expr()->like("$rootAlias.name", ":g_search"),
                    $qb->expr()->like("$rootAlias.phone", ":g_search"),
                    $qb->expr()->like("$rootAlias.city", ":g_search"),
                    $qb->expr()->like("$rootAlias.address", ":g_search"),
                    $qb->expr()->like("$rootAlias.zipcode", ":g_search"),
                    $qb->expr()->like("service.name", ":g_search"),
                    $qb->expr()->like("intercommunal.name", ":g_search"),
                    $qb->expr()->like("town.name", ":g_search"),
                    $qb->expr()->like("user.username", ":g_search"),
                    $qb->expr()->like("user.email", ":g_search"),
                    $qb->expr()->like("groups.name", ":g_search")
                )
            )->setParameter('g_search', '%' . $searchValue . '%');

            // Clear global search so the Trait doesn't conflict with our custom logic
            $params['search']['value'] = '';
        }

        // Delegate pagination and final execution to the Generic Trait
        return $this->findForDatatable($qb, $params);
    }

    /*******************************************************************************************/
    /********************* Stats methods *****/
    /*******************************************************************************************/

    /**
     * Stat for overall reports
     * Use on overall Stat
     */
    public function statAdherent($adherent = null, $town = null, $interco = null, $service = null): mixed
    {
        $qb = $this->queryAdherent();

        if ($adherent != null && count($adherent) > 0) {
            $qb->andWhere($qb->expr()->in('adherent', ':q_adherent'))
                ->setParameter(':q_adherent', $adherent);
        }

        if ($town != null && count($town) > 0) {
            $qb->andWhere($qb->expr()->in('town', ':q_town'))
                ->setParameter(':q_town', $town);
        }

        if ($interco != null && count($interco) > 0) {
            $qb->andWhere($qb->expr()->in('intercommunal', ':q_intercommunal'))
                ->setParameter(':q_intercommunal', $interco);
        }

        if ($service != null && count($service) > 0) {
            $qb->andWhere($qb->expr()->in('service', ':q_service'))
                ->setParameter(':q_service', $service);
        }

        $qb->select(array(
            'partial adherent.{id, name, firstname, function, service, town, intercommunal, city}',
            'partial town.{id, name}',
        ));

        return $qb->getQuery()->getResult();
    }

    /*******************************************************************************************/
    /********************* Custom find methods *****/
    /*******************************************************************************************/

    /**
     * Find max num used for adherent
     * Use on code generator
     */
    public function findMaxUsername($prefix): mixed
    {
        $qb = $this->queryAdherentWithoutDepartment();

        $qb->andWhere($qb->expr()->like('user.username', ':username'))
            ->setParameter('username', '%' . $prefix . '%');

        $qb->select($qb->expr()->max('user.username'));

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Adherent Repository - ' . $e->getMessage();

            return false;
        }
    }

    /**
     * Find one adherent with email param
     * Used in LuccaImportAdherentCommand
     */
    public function findOneByEmail($email): mixed
    {
        $qb = $this->queryAdherent();

        $qb->andWhere($qb->expr()->eq('user.email', ':email'))
            ->setParameter('email', $email);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Adherent Repository - ' . $e->getMessage();

            return false;
        }
    }

    /**
     * Find one adherent with User entity
     */
    public function findOneByUser(User $user): ?object
    {
        $qb = $this->queryAdherent();

        $qb->andWhere($qb->expr()->eq('user', ':q_user'))
            ->setParameter('q_user', $user);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Adherent Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Custom get methods *****/
    /*******************************************************************************************/

    /**
     * Override function to add join to User Entity
     */
    public function getValuesActive(bool $enabled = true): QueryBuilder
    {
        $qb = $this->queryAdherent();

        $qb->orderBy('adherent.name', 'ASC');

        $qb->andWhere($qb->expr()->eq('adherent.enabled', ':q_enabled'))
            ->setParameter('q_enabled', $enabled);

        return $qb;
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with Adherent dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryAdherent();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with Adherent dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryAdherent();

        $qb->andWhere($qb->expr()->eq('adherent.id', ':q_adherent'))
            ->setParameter(':q_adherent', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - Adherent Repository - ' . $e->getMessage();

            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of Minute Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryAdherent(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('adherent')
            ->leftJoin('adherent.user', 'user')->addSelect('user')
            ->leftJoin('user.groups', 'groups')->addSelect('groups')
            ->leftJoin('adherent.town', 'town')->addSelect('town')
            ->leftJoin('adherent.intercommunal', 'intercommunal')->addSelect('intercommunal')
            ->leftJoin('adherent.service', 'service')->addSelect('service')
            ->leftJoin('adherent.department', 'department')
        ;

        $department = $this->userDepartmentResolver->getDepartment();
        if ($department) {
            $qb->andWhere($qb->expr()->eq('department', ':q_department'))
                ->setParameter(':q_department', $department);
        }

        return $qb;
    }

    /**
     * Classic dependencies
     */
    private function queryAdherentWithoutDepartment(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('adherent')
            ->leftJoin('adherent.user', 'user')->addSelect('user')
            ->leftJoin('user.groups', 'groups')->addSelect('groups')
            ->leftJoin('adherent.town', 'town')->addSelect('town')
            ->leftJoin('adherent.intercommunal', 'intercommunal')->addSelect('intercommunal')
            ->leftJoin('adherent.service', 'service')->addSelect('service')
            ->leftJoin('adherent.department', 'department')
        ;

        return $qb;
    }
}
