<?php declare(strict_types=1);

namespace ShipEngine;

use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Service\Address\AddressService;
use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\Util;
use ShipEngine\Util\ShipEngineLogger;

/**
 * Exposes the functionality of the ShipEngine API.
 *
 * @package ShipEngine
 */
final class ShipEngine
{
    use Util\Getters;

    /**
     * A collection of methods to call the ShipEngine Address Validation Services.
     *
     * @var AddressService
     */
    private AddressService $address_service;

    /**
     * Instantiates the ShipEngine API client used for all HTTP Requests, unless
     * a custom client has been passed in using configuration options.
     *
     * @var ShipEngineClient
     */
    private ShipEngineClient $shipengine;

    /**
     * Global configuration for the ShipEngine API client, such as timeouts,
     * retries, page size, etc. This configuration applies to all method calls,
     * unless specifically overridden when calling a method.
     *
     * @var ShipEngineConfig
     */
    private ShipEngineConfig $config;

    /**
     * ShipEngineLogger class.
     *
     * @var ShipEngineLogger
     */
    private ShipEngineLogger $logger;


    /**
     * Instantiates the ShipEngine class. The `api_key` you pass in can be either
     * a ShipEngine sandbox or production API Key. (sandbox keys start with "TEST_)
     *
     * @param mixed $config Can be either a string that is your `api_key` or an `array` {api_key:string,
     * base_url:string, page_size:int, retries:int, timeout:int, client:HttpClient|null}
     */
    public function __construct($config = null)
    {
        $this->config = new ShipEngineConfig(
            is_string($config) ? array('api_key' => $config) : $config
        );
        $this->shipengine = new ShipEngineClient($this->config);
        $this->address_service = new AddressService($this->shipengine);
    }

    // TODO: change return object from DTO -> a return type.
    /**
     * Validate an address sin nearly any country in the world.
     *
     * @param Address $address The address to validate. This can even be an incomplete or improperly formatted address.
     * @param array|null $config Optional configuration overrides for this method call {api_key:string,
     * base_url:string, page_size:int, retries:int, timeout:int, client:HttpClient|null}
     * @return AddressValidateResult
     */
    public function validateAddress(Address $address, array $config = null): AddressValidateResult
    {
        $config = $this->config->merge($config);

        return $this->address_service->validate($address, $config);
    }
}
