<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\Service\Address\AddressService;
use ShipEngine\Service\Tag\TagService;
use ShipEngine\ShipEngineClient;

/**
 * Instantiate and attach services to the \ShipEngine\ShipEngine client.
 */
class ServiceFactory
{
    /**
     * @var ShipEngineClient
     */
    private ShipEngineClient $client;

    /**
     * @var string[]
     */
    private $classes = [
        'tags' => TagService::class,
        'addresses' => AddressService::class
    ];

    /**
     * @var array
     */
    private $services = array();

    /**
     * ServiceFactory constructor.
     * @param ShipEngineClient $client
     */
    public function __construct(ShipEngineClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $name
     * @return mixed
     */
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
