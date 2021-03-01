<?php declare(strict_types=1);

namespace ShipEngine;

use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A wrapped HTTP client.
 */
final class ShipEngineClient
{

    const DEFAULT_BASE_URI = 'https://api.shipengine.com/v1/';

    private PluginClient $client;

    public function __construct(string $api_key, string $user_agent, HttpClient $client = null)
    {
        if (!$client) {
            $client = HttpClientDiscovery::find();
        }

        $headers = array();
        $headers['Api-Key'] = $api_key;
        $headers['User-Agent'] = $user_agent;

        $uri_factory = UriFactoryDiscovery::find();
        $base_uri = $uri_factory->createUri(getenv('RPC_CLIENT_BASE_URI') ?? self::DEFAULT_BASE_URI);

        $plugins = array();
        $plugins[] = new HeaderDefaultsPlugin($headers);
        $plugins[] = new BaseUriPlugin($base_uri);
        $plugins[] = new RetryPlugin([
            'retries' => 1,
            'error_response_decider' => function (RequestInterface $request, ResponseInterface $response): bool {
                $status = $response->getStatusCode();
                return $status === 429;
            }
        ]);

        $this->client = new PluginClient($client, $plugins);
    }

    /**
     * Send an HTTP request.
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
