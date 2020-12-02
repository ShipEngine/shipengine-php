<?php declare(strict_types=1);

namespace ShipEngine;

use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A wrapped HTTP client.
 */
final class ShipEngineClient
{

    private array $config;
    private PluginClient $client;
    
    public function __construct(HttpClient $client, array $plugins, array $config)
    {
        $this->client = new PluginClient($client, $plugins);
        $this->config = $config;
    }

    /**
     * Send an HTTP request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
