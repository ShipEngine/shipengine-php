<?php declare(strict_types=1);

namespace ShipEngine;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\Model\Package\Package;
use ShipEngine\Model\Package\TrackingQuery;
use ShipEngine\Model\Package\TrackPackageResult;
use ShipEngine\Service\Address\AddressService;
use ShipEngine\Service\Carriers\CarrierAccountService;
use ShipEngine\Service\Package\TrackPackageService;
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

    private TrackPackageService $tracking_service;

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
     * base_url:string, page_size:int, retries:int, timeout:int, event_listener:object}
     */
    public function __construct($config = null)
    {
        $this->config = new ShipEngineConfig(
            is_string($config) ? array('api_key' => $config) : $config
        );
        $this->address_service = new AddressService();
        $this->tracking_service = new TrackPackageService();
    }

    /**
     * Validate an address in nearly any country in the world.
     *
     * @param Address $address The address to validate. This can even be an incomplete or improperly formatted address.
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {api_key:string,
     * base_url:string, page_size:int, retries:int, timeout:int, client:HttpClient|null}
     * @return AddressValidateResult
     * @throws ShipEngineException|ClientExceptionInterface
     */
    public function validateAddress(Address $address, $config = null): AddressValidateResult
    {
        $config = $this->config->merge($config);

        return $this->address_service->validate($address, $config);
    }


    /**
     * Normalize a given address into a standardized format used by carriers.
     *
     * @param Address $address
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {api_key:string,
     * base_url:string, page_size:int, retries:int, timeout:int, client:HttpClient|null}
     * @return Address
     * @throws ShipEngineException|ClientExceptionInterface
     */
    public function normalizeAddress(Address $address, $config = null): Address
    {
        $config = $this->config->merge($config);

        return $this->address_service->normalize($address, $config);
    }

    /**
     * Get all carrier accounts for a given ShipEngine Account.
     *
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {api_key:string,
     * base_url:string, page_size:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array An array of **CarrierAccount** objects that correspond the to carrier accounts connected
     * to a given ShipEngine account.
     * @throws ShipEngineException|ClientExceptionInterface
     */
    public function getCarrierAccounts($config = null): array
    {
        $config = $this->config->merge($config);

        return (new CarrierAccountService())->fetchCarrierAccounts($config);
    }

    /**
     * Track a package by `tracking_number` and `carrier_code` via the **TrackingQuery** object, by using just the
     * **package_id**, or by using a **Package** object.
     *
     * @param TrackingQuery|null $tracking_data
     * @param string|null $package_id
     * @param Package|null $package
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {api_key:string,
     * base_url:string, page_size:int, retries:int, timeout:int, client:HttpClient|null}
     * @return Model\Package\TrackPackageResult
     * @throws ClientExceptionInterface
     */
    public function trackPackage(
        ?TrackingQuery $tracking_data = null,
        ?string $package_id = null,
        ?Package $package = null,
        $config = null
    ): TrackPackageResult {
        $config = $this->config->merge($config);

        return $this->tracking_service->track($config, $tracking_data, $package_id, $package);
    }
}
