<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Service\Aigle;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Lucca\Bundle\CoreBundle\Service\Aigle\AigleMinuteStatusResolver;
use Lucca\Bundle\MinuteBundle\Entity\Closure;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class FolderChangeStatusAigleNotifier
{
    public function __construct(
        private AigleMinuteStatusResolver $statusResolver,
        private LoggerInterface $logger,
        private AigleApiClient $aigleApiClient,

    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function notifyAigle(Minute $minute): void
    {
        $parcelRaw = $minute->getPlot()?->getParcel();
        $town = $minute->getPlot()->getTown();
        $responses = [];

        if (!$parcelRaw) {
            $this->logger->info('Minute has no parcel, skipping Aigle notification.');
            return;
        }

        $parcels = array_map('trim', explode(',', $parcelRaw));
        $aigleStatus = $this->statusResolver->statusResolver($minute);

        if (!$aigleStatus || empty($parcels)) {
            $this->logger->info('No Aigle status resolved or no parcels found, skipping Aigle notification.');
            return;
        }

        // One request per parcel
        foreach ($parcels as $parcel) {
            $responses[$parcel] = $this->aigleApiClient->post(
                '/update-control-status'
                , [
                    'insee_code' => $town->getCode(),
                    'parcel_code' => $parcel,
                    'control_status' => $aigleStatus,
                ]
            );
        }

        // Log responses
        /**
         * @var string $parcel
         * @var ResponseInterface $response
         */
        foreach ($responses as $parcel => $response) {
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false); // false to avoid exceptions on non-2xx responses

            if ($statusCode >= 200 && $statusCode < 300) {
                $this->logger->info(sprintf(
                    'Aigle notification successful for parcel %s, in town %d: status %d, response: %s',
                    $parcel,
                    $town->getName(),
                    $statusCode,
                    $content
                ));
            } else {
                $this->logger->error(sprintf(
                    'Aigle notification failed for parcel %s, in town %d: status %d, response: %s',
                    $parcel,
                    $town->getName(),
                    $statusCode,
                    $content
                ));
            }
        }
    }
}
