<?php declare(strict_types=1);

namespace ShipEngine;

use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
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

    private ShipEngineConfig $config;
    private PluginClient $client;
   
    public function __construct(ShipEngineConfig $config, HttpClient $client = null)
    {
        $this->config = $config;
        
        if (!$client) {
            $client = HttpClientDiscovery::find();
        }
        
        $headers = array();
        $headers['Api-Key'] = $config->api_key;
        $headers['User-Agent'] = $config->user_agent;

        $uri_factory = UriFactoryDiscovery::find();
        $base_uri = $uri_factory->createUri($config->base_uri);
        
        $plugins = array();
        $plugins[] = new HeaderDefaultsPlugin($headers);
        $plugins[] = new BaseUriPlugin($base_uri);
        $plugins[] = new RetryPlugin([
            'retries' => $config->retries,
            'error_response_decider' => function (RequestInterface $request, ResponseInterface $response): bool {
                $status = $response->getStatusCode();
                return $status === 429;
            }
        ]);

        $this->client = new PluginClient($client, $plugins);
    }
    
    /**
     * Send an HTTP request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
