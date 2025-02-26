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
}

