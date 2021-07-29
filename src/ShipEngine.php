<?php declare(strict_types=1);

namespace ShipEngine;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Model\Package\TrackPackageResult;
use ShipEngine\Service\Address\AddressService;
use ShipEngine\Service\Carriers\CarrierAccountService;
use ShipEngine\Service\Package\TrackPackageService;
use ShipEngine\Util\ShipEngineLogger;

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

    /**
     * A collection of methods to call the ShipEngine Address Validation Services.
     *
     * @var AddressService
     */
    protected AddressService $addressService;

    /**
     * Methods that allow you to track a package by **packageId** or by *trackingNumber* and **carrierCode** using
     * an instance of the **TrackingQuery** class that has those properties.
     *
     * @var TrackPackageService
     */
    protected TrackPackageService $trackingService;

    /**
     * Global configuration for the ShipEngine API client, such as timeouts,
     * retries, page size, etc. This configuration applies to all method calls,
     * unless specifically overridden when calling a method.
     *
     * @var ShipEngineConfig
     */
    public ShipEngineConfig $config;

    /**
     * ShipEngineLogger class.
     *
     * @var ShipEngineLogger
     */
    protected ShipEngineLogger $logger;

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
        $this->addressService = new AddressService();
        $this->trackingService = new TrackPackageService();
    }

    /**
     * Validate an address in nearly any countryCode in the world.
     *
     * @param Address $address The address to validate. This can even be an incomplete or improperly formatted address.
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return AddressValidateResult
     * @throws ShipEngineException|ClientExceptionInterface
     */
    public function validateAddress(Address $address, $config = null): AddressValidateResult
    {
        $config = $this->config->merge($config);

        return $this->addressService->validate($address, $config);
    }

    /**
     * Fetch the carrier accounts connected to your ShipEngine Account.
     *
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @param string|null $carrierCode
     * @return array An array of **CarrierAccount** objects that correspond the to carrier accounts connected
     * to a given ShipEngine account.
     */
    public function getCarrierAccounts(?string $carrierCode = null, $config = null): array
    {
        $config = $this->config->merge($config);

        return CarrierAccountService::fetchCarrierAccounts($config, $carrierCode);
    }

    /**
     * Track a package by `trackingNumber` and `carrierCode` via the **TrackingQuery** object, by using just the
     * **packageId**.
     *
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return Model\Package\TrackPackageResult
     * @throws ClientExceptionInterface
     */
    public function trackPackage($tracking_data, $config = null): TrackPackageResult
    {
        $config = $this->config->merge($config);

        return $this->trackingService->track($config, $tracking_data);
    }
}
