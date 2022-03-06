<?php

namespace Sirvantos\MyEmailVerifier\Services\Suppliers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Sirvantos\MyEmailVerifier\Services\Suppliers\Contracts\CanSupply;
use Sirvantos\MyEmailVerifier\Services\Suppliers\Exceptions\SupplierException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class GuzzleClient.
 */
class Guzzle implements CanSupply
{
    private ?Client $client = null;

    private array $payload;

    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function supply(array $array): object
    {
        return $this->request($array['method'], $array['uri'], $array['options'] ?? []);
    }

    private function initClient(): Client
    {
        return $this->client ?: $this->client = app(Client::class, $this->getPayload());
    }

    /**
     * @param $method
     * @param string $uri
     * @param array $options
     *
     * @return ResponseInterface
     */
    private function request($method, $uri = '', array $options = []): ResponseInterface
    {
        try {
            return $this->initClient()->request($method, $uri, $options + ['http_errors' => true]);
        } catch (ClientException $e) {
            throw new SupplierException('Supplier exception', 0, $e);
        }
    }
}
