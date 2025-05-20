<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Manager;

use Doctrine\ORM\EntityManagerInterface,
    Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface,
    Symfony\Component\HttpFoundation\Request;

use Lucca\Bundle\SecurityBundle\Entity\LoginAttempt;
use Lucca\Bundle\SecurityBundle\Repository\LoginAttemptRepository;

readonly class LoginAttemptManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ParameterBagInterface  $parameterBag
    )
    {
    }

    /**
     * Approve specific ip with time range configured in Security parameters
     */
    public function approveIp(string $p_ip): array
    {
        $now = new \DateTime('now');
        $periodScanLimitToClean = $this->parameterBag->get('lucca_security.protection')['period_clean_in_day'];

        /** @var LoginAttemptRepository $repository */
        $repository = $this->em->getRepository(LoginAttempt::class);
        $attempts = $repository->findAllByIpAddressAndLoginAttemptDate(
            $p_ip, $now->modify('-' . $periodScanLimitToClean . ' days')
        );

        /** @var LoginAttempt $attempt */
        foreach ($attempts as $attempt) {
            $attempt->setIsCleared(true);
            $this->em->persist($attempt);
        }

        $this->em->flush();

        return $attempts;
    }

    /**
     * Create a new LoginAttempt with a specific Request
     */
    public function createLoginAttempt(Request $p_request): LoginAttempt
    {
        /** Create a LoginAttempt and register all information request */
        $loginAttempt = new LoginAttempt();

        $xForwardedForStr = $p_request->headers->get('x-forwarded-for');

        /** keep the first IP address in the list if exist (the first IP is the public client IP address) */
        if ($xForwardedForStr)
            $requestIp = explode(',', $xForwardedForStr)[0] ?? $p_request->getClientIp();
        else
            $requestIp = $p_request->getClientIp();

        $loginAttempt->setRequestIp($requestIp);
        $loginAttempt->setClientIps(json_encode($p_request->getClientIps()));

        $loginAttempt->setRequestUri($p_request->getRequestUri());
        $loginAttempt->setRequestedAt(new \DateTime('now'));

        if ($p_request->request->has('username'))
            $loginAttempt->setUsername($p_request->request->get('username'));

        $loginAttempt->setControllerAsked($p_request->attributes->get('_controller'));
        $loginAttempt->setFirewall($p_request->attributes->get('_firewall_context'));

        $loginAttempt->setAgent($p_request->server->get('HTTP_USER_AGENT'));
        $loginAttempt->setHost($p_request->server->get('HTTP_HOST'));

        $loginAttempt->setSoftware($p_request->server->get('SERVER_SOFTWARE'));
        $loginAttempt->setPort($p_request->server->get('SERVER_PORT'));

        /** Server address */
        if ($p_request->headers->get('x-forwarded-for'))
            $address = $p_request->headers->get('x-forwarded-for');
        else if ($p_request->server->get('HEADER_X_FORWARDED_FOR'))
            $address = $p_request->server->get('HEADER_X_FORWARDED_FOR');
        else if ($p_request->server->get('HEADER_X_FORWARDED_HOST'))
            $address = $p_request->server->get('HEADER_X_FORWARDED_HOST');
        else
            $address = $p_request->server->get('SERVER_NAME');

        $loginAttempt->setAddress($address);

        $loginAttempt->setAddressRemote($p_request->server->get('REMOTE_ADDR'));
        $loginAttempt->setPortRemote($p_request->server->get('REMOTE_PORT'));

        $loginAttempt->setRequestTime($p_request->server->get('REQUEST_TIME'));

        $this->em->persist($loginAttempt);
        $this->em->flush();

        return $loginAttempt;
    }
}
