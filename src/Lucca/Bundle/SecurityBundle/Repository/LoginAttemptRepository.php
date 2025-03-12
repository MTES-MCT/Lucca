<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class LoginAttemptRepository extends EntityRepository
{
    /**
     * Find all LoginAttempt entities by ipAddress and since timeLimit
     */
    public function findAllByIpAddressAndLoginAttemptDate(string $p_ipAddress, \DateTime $p_timeLimit): array
    {
        $qb = $this->createQueryBuilder('login_attempt');

        $qb
            ->where($qb->expr()->andX(
                $qb->expr()->eq('login_attempt.requestIp', ':q_ip'),
                $qb->expr()->gt('login_attempt.requestedAt', ':q_requestedAt')
            ))
            ->andWhere($qb->expr()->eq('login_attempt.isCleared', ':q_isCleared'))
            ->setParameter('q_ip', $p_ipAddress)
            ->setParameter('q_requestedAt', $p_timeLimit)
            ->setParameter('q_isCleared', false);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all thirdParties with mail, name, firstname, phone, ...
     */
    public function findForDatatable(?string $columnOrder, ?string $dirOrder, int $start, int $length, ?string $requestedAt,
                                     ?string $requestIp, ?string $requestUri, ?string $username, ?string $address, ?string $addressRemote): array
    {
        $qb = $this->createQueryBuilder('login_attempt');

        if ($requestedAt) {
            $qb->andWhere($qb->expr()->like('login_attempt.requestedAt', ':q_requestedAt'))
                ->setParameter(':q_requestedAt', $requestedAt . '%');
        }

        if ($requestIp) {
            $qb->andWhere($qb->expr()->like('login_attempt.requestIp', ':q_requestIp'))
                ->setParameter(':q_requestIp', '%' . $requestIp . '%');
        }

        if ($requestUri) {
            $qb->andWhere($qb->expr()->like('login_attempt.requestUri', ':q_requestUri'))
                ->setParameter(':q_requestUri', '%' . $requestUri . '%');
        }

        if ($username) {
            $qb->andWhere($qb->expr()->like('login_attempt.username', ':q_username'))
                ->setParameter(':q_username', '%' . $username . '%');
        }

        if ($address) {
            $qb->andWhere($qb->expr()->like('login_attempt.address', ':q_address'))
                ->setParameter(':q_address', '%' . $address . '%');
        }

        if ($addressRemote) {
            $qb->andWhere($qb->expr()->like('login_attempt.addressRemote', ':q_addressRemote'))
                ->setParameter(':q_addressRemote', '%' . $addressRemote . '%');
        }

        /** Order by */
        if ($columnOrder != null && $dirOrder !== null) {
            $qb->orderBy('login_attempt.' . $columnOrder, $dirOrder);
        }

        $qbCount = clone $qb;

        /** Select partial fields to avoid memory leak, rename all column for order by */
        $qb->select([
            'partial login_attempt.{id, requestedAt, requestIp, requestUri, username, address, addressRemote, isCleared}',
        ]);

        $loginAttempts =
            /** Limit results */
            $qb->setFirstResult($start)
                ->setMaxResults($length)
                ->getQuery()->getArrayResult();

        try {
            $count = $qbCount->select('count(login_attempt.id)')
                ->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            $count = count($loginAttempts);
        }

        return ['loginAttempts' => $loginAttempts, 'count' => $count];
    }
}

