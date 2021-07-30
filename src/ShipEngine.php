<?php declare(strict_types=1);

namespace ShipEngine;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\ListCarriers\Result as ListCarriersResult;

/**
 * Exposes the functionality of the ShipEngine API.
 *
 * @package ShipEngine
 */
final class ShipEngine
{
    /**
     * ShipEngine SDK Version
     */
    public const VERSION = '0.0.1';

    // /**
    //  *
    //  * @var ShipEngineClient
    //  */
    // protected ShipEngineClient $client;

    /**
     * Global configuration for the ShipEngine API client, such as timeouts,
     * retries, page size, etc. This configuration applies to all method calls,
     * unless specifically overridden when calling a method.
     *
     * @var ShipEngineConfig
     */
    public ShipEngineConfig $config;

    /**
     * Instantiates the ShipEngine class. The `apiKey` you pass in can be either
     * a ShipEngine sandbox or production API Key. (sandbox keys start with "TEST_)
     *
     * @param mixed $config Can be either a string that is your `apiKey` or an `array` {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, eventListener:object}
     */
    public function __construct($config = null)
    {
        $this->config = new ShipEngineConfig(
            is_string($config) ? array('apiKey' => $config) : $config
        );
    }

    /**
     * Fetch the carrier accounts connected to your ShipEngine Account.
     *
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array An array of **CarrierAccount** objects that correspond the to carrier accounts connected
     * to a given ShipEngine account.
     */
    public function listCarriers($config = null): array
    {
        $config = $this->config->merge($config);
        $client = new ShipEngineClient();
        $apiResponse = $client->get(
            'v1/carriers',
            $config,
        );

        return $apiResponse;
    }
}
