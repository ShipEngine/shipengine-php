<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\ShipEngineClient;

/**
 * Instantiate and attach services to the \ShipEngine\ShipEngine client.
 */
class ServiceFactory
{
    private ShipEngineClient $client;

    private $classes = [
        'tags' => TagsService::class
    ];

    private $services = array();

    public function __construct(ShipEngineClient $client)
    {
        $this->client = $client;
    }

    public function __get(string $name)
    {
        if (!array_key_exists($name, $this->classes)) {
            throw new \BadMethodCallException($name . ' service does not exist.');
        }

        if (!array_key_exists($name, $this->services)) {
            $this->services[$name] = new $this->classes[$name]($this->client);
        }
        return $this->services[$name];
    }
}