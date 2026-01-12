<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Service\Aigle;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Psr\Log\LoggerInterface;

use Lucca\Bundle\CoreBundle\Exception\AigleNotificationException;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
readonly class MinuteChangeStatusAigleNotifier
{
    public function __construct(
        private AigleMinuteStatusResolver $statusResolver,
        private LoggerInterface $logger,
        private AigleApiClient $aigleApiClient,

    ) {}

    /**
     * @throws AigleNotificationException
     */
    public function updateAigleMinuteStatus(Minute $minute): void
    {

        $parcelRaw = $minute->getPlot()?->getParcel();
        $town = $minute->getPlot()->getTown();
        $responses = [];

        if (!$parcelRaw) {
            $this->logger->error('AigleApi: Minute has no parcel, skipping Aigle notification.');
            throw AigleNotificationException::noParcelsFound();
        }

        $parcels = array_map('trim', explode(',', $parcelRaw));
        $aigleStatus = $this->statusResolver->statusResolver($minute);

        if (!$aigleStatus) {
            $this->logger->warning('AigleApi: No Aigle status resolved, for minute '.$minute->getNum().', with status '.$minute->getStatus().', skipping Aigle notification.');
            return;
        }

        if (empty($parcels)) {
            $this->logger->error('AigleApi: No parcels found, maybe due to parsing error, skipping Aigle notification.');
            throw AigleNotificationException::malFormedParcelString($parcelRaw);
        }

        try {
            // One request pe parcel
            foreach ($parcels as $parcel) {
                $responses[$parcel] = $this->aigleApiClient->post(
                    '/update-control-status/'
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
                        'AigleApi: Aigle notification successful for parcel %s, in town %d: status %d, aigle status %s, response: %s',
                        $parcel,
                        $town->getName(),
                        $statusCode,
                        $aigleStatus,
                        $content
                    ));
                } else {
                    $this->logger->error(sprintf(
                        'AigleApi: Aigle notification failed for parcel %s, in town %d: status %d, aigle status %s, response: %s',
                        $parcel,
                        $town->getName(),
                        $statusCode,
                        $aigleStatus,
                        $content
                    ));
                    throw AigleNotificationException::errorDuringNotification();
                }
            }
        } catch (\Exception|\Throwable $e) {
            $this->logger->error(sprintf(
                'AigleApi: Aigle notification error for minute %d, town %d: %s',
                $minute->getId(),
                $town->getName(),
                $e->getMessage()
            ));
            throw AigleNotificationException::errorDuringNotification($e);
        }
    }
}
