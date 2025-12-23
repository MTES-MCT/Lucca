<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Service\Aigle;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class AigleApiClient
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(param: 'lucca_core.aigle_api_base_url')]
        private string              $baseUrl,
        #[Autowire(param: 'lucca_core.aigle_api_key')]
        private string              $apiKey,
    ) {
    }
    /**
     * Generic request method
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $endpoint, array $options = []): ResponseInterface
    {
        // add header Authorization
        $options['headers']['Authorization'] = 'Api-Key '.$this->apiKey;

        // set default Content-Type to application/json if not set and no body is provided
        if (!isset($options['json']) && !isset($options['body'])) {
            $options['headers']['Content-Type'] = 'application/json';
        }

        return $this->client->request(
            $method,
            $this->baseUrl . '/' . ltrim($endpoint, '/'),
            $options
        );
    }

    /** shortcut methods *
     * @throws TransportExceptionInterface
     */
    public function get(string $endpoint, array $query = []): ResponseInterface
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function post(string $endpoint, array $data = []): ResponseInterface
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function put(string $endpoint, array $data = []): ResponseInterface
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function patch(string $endpoint, array $data = []): ResponseInterface
    {
        return $this->request('PATCH', $endpoint, ['json' => $data]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function delete(string $endpoint): ResponseInterface
    {
        return $this->request('DELETE', $endpoint);
    }
}
