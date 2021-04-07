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
use ShipEngine\Service\ShipEngineConfig;

/**
 * A wrapped `JSON-RPC 2.0` HTTP client.
 *
 * @pacakge ShipEngine
 */
final class ShipEngineClient
{
    /**
     * @var PluginClient
     */
    private PluginClient $client;

    private array $plugins;

    private ShipEngineConfig $config;

    /**
     * ShipEngineClient constructor, this is the global client and is used if a custom
     * client is not passed in via configuration options.
     *
     * @param ShipEngineConfig $config
     * @param string $user_agent
     * @param HttpClient|null $client
     */
    public function __construct(ShipEngineConfig $config, string $user_agent, HttpClient $client = null)
    {
        $this->config = $config;

        if (!$client) {
            $client = HttpClientDiscovery::find();
        }

        $headers = array();
        $headers['Api-Key'] = $config->api_key;
        $headers['User-Agent'] = $user_agent;
        $headers['Content-Type'] = 'application/json';

        $uri_factory = UriFactoryDiscovery::find();

        if (!getenv('CLIENT_BASE_URI')) {
            $base_url = $config->base_url;
        } else {
            $base_url = getenv('CLIENT_BASE_URI');
        }

        $base_uri = $uri_factory->createUri($base_url);

        $this->plugins = array();
        $this->plugins[] = new HeaderDefaultsPlugin($headers);
        $this->plugins[] = new BaseUriPlugin($base_uri);
        $this->plugins[] = new RetryPlugin([
            'retries' => $config->retries,
            'error_response_decider' => function (RequestInterface $request, ResponseInterface $response): bool {
                $status = $response->getStatusCode();
                return $status === 429;
            }
        ]);

        $this->client = new PluginClient($client, $this->plugins);
    }

    /**
     * Send a `JSON-RPC 2.0` request via HTTP Messages to ShipEngine API. If the response
     * is successful, the result is returned. Otherwise, an error is thrown..
     *
     * @param RequestInterface $request
     * @param ShipEngineConfig $config
     * @return ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function sendRequest(RequestInterface $request, ShipEngineConfig $config): ResponseInterface
    {
        //TODO: implement the use of the passed in $config
        return $this->client->sendRequest($request);
    }
}
