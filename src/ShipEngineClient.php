<?php declare(strict_types=1);

namespace ShipEngine;

use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;

/**
 *
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

    public function __get($name)
    {
        $this->client->__get($name);
    }
}
