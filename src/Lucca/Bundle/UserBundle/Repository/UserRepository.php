<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Repository;

use Doctrine\ORM\{NonUniqueResultException, QueryBuilder};
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\{PasswordUpgraderInterface, PasswordAuthenticatedUserInterface};

use Lucca\Bundle\CoreBundle\Repository\LuccaRepository;
use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\CoreBundle\Repository\Partial\DataTableTrait;

class UserRepository extends LuccaRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    use DataTableTrait;

    /**
     * Find User who can authenticated
     * By username or email
     */
    public function loadUserByIdentifier(string $identifier): ?User
    {
        $qb = $this->queryUser();

        $qb->where($qb->expr()->eq('user.enabled', ':q_enabled'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->eq('user.username', ':q_identifier'),
                $qb->expr()->eq('user.email', ':q_identifier')
            ))
            ->setParameter(':q_enabled', true)
            ->setParameter(':q_identifier', $identifier);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * This function is used to search user in all the database, with less filter
     * Used when try to activate a User with Command line
     */
    public function findByUsernameOrEmail($identifier): ?User
    {
        $qb = $this->queryUser();

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('user.username', ':q_identifier'),
            $qb->expr()->eq('user.email', ':q_identifier')
        ))
            ->setParameter(':q_identifier', $identifier);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - User Repository - ' . $e->getMessage();
            return null;
        }
    }

    /*******************************************************************************************/
    /********************* Override basic methods *****/
    /*******************************************************************************************/

    /**
     * Override findAll method
     * with User dependencies
     */
    public function findAll(): array
    {
        $qb = $this->queryUser();

        return $qb->getQuery()->getResult();
    }

    /**
     * Override find method
     * with User dependencies
     */
    public function find(mixed $id, $lockMode = null, $lockVersion = null): ?object
    {
        $qb = $this->queryUser();

        $qb->where($qb->expr()->eq('user.id', ':q_user'))
            ->setParameter(':q_user', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            echo 'NonUniqueResultException has been thrown - User Repository - ' . $e->getMessage();

            return null;
        }
    }

    /**
     * Custom datatable search for Users with Adherent Departments
     */
    public function searchUsersForDatatable(array $params): array
    {
        $qb = $this->queryUser()
            // Important: Join Adherents and their Departments
            ->leftJoin('user.adherents', 'adherents')->addSelect('adherents')
            ->leftJoin('adherents.department', 'dept')->addSelect('dept');

        $rootAlias = $qb->getRootAliases()[0];

        // --- 1. ORDERING ---
        if (isset($params['order'][0])) {
            $colIndex = $params['order'][0]['column'];
            $colName = $params['columns'][$colIndex]['data'];
            $dir = $params['order'][0]['dir'];

            // Handling virtual or joined columns
            if (in_array($colName, ['groups', 'departments', 'actions', 'enabled'])) {
                if ($colName === 'departments') {
                    $qb->orderBy("dept.name", $dir);
                } elseif ($colName === 'groups') {
                    $qb->orderBy("groups.name", $dir);
                } elseif ($colName === 'actions' || $colName === 'enabled') {
                    $qb->orderBy("$rootAlias.enabled", $dir);
                }
                unset($params['order']);
            }
        }

        // --- 2. SEARCH ---
        $searchValue = $params['search']['value'] ?? null;
        if (!empty($searchValue)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like("$rootAlias.username", ":g_search"),
                    $qb->expr()->like("$rootAlias.email", ":g_search"),
                    $qb->expr()->like("$rootAlias.name", ":g_search"),
                    $qb->expr()->like("dept.name", ":g_search"),
                    $qb->expr()->like("groups.name", ":g_search")
                )
            )->setParameter('g_search', '%' . $searchValue . '%');

            $params['search']['value'] = '';
        }

        return $this->findForDatatable($qb, $params);
    }

    /*******************************************************************************************/
    /********************* Specific request generated by Symfony maker *****/
    /*******************************************************************************************/

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /*******************************************************************************************/
    /********************* Query - Dependencies of User Entity *****/
    /*******************************************************************************************/

    /**
     * Classic dependencies
     */
    private function queryUser(): QueryBuilder
    {
        return $this->createQueryBuilder('user')
            ->leftJoin('user.groups', 'groups')->addSelect('groups');
    }
}
