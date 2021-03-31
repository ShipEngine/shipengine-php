<?php declare(strict_types=1);

namespace ShipEngine;

use Http\Client\HttpClient;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressResult;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Service\Address\AddressTrait;
use ShipEngine\Service\Package\PackageTrackingTrait;
use ShipEngine\Service\ServiceFactory;
use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\Service\Tag\TagTrait;
use ShipEngine\Service\Address\AddressService;
use ShipEngine\Util;

/**
 * ShipEngine RPC 2.0 client.
 *
 * @package ShipEngine
 */
final class ShipEngine
{
    use Util\Getters;

    private AddressService $address_service;

    private ShipEngineClient $shipengine;

    private ShipEngineConfig $config;

    /**
     *
     */
    const VERSION = '0.0.1';

    /**
     * ShipEngine constructor.
     *
     * @param ShipEngineConfig $config
     * @param HttpClient|null $client
     */
    public function __construct(ShipEngineConfig $config, HttpClient $client = null)
    {
        $this->config = $config;
        $user_agent = $this->deriveUserAgent();
        $this->shipengine = new ShipEngineClient($config, $user_agent, $client);
        $this->address_service = new AddressService($this->shipengine);
    }

    /**
     * @param Address $address
     * @param array|null $config {api_key:string, retries:int, timeout:int}|null
     * @return AddressValidateResult
     */
    public function validateAddress(Address $address, ?array $config = null): AddressValidateResult
    {
        if (isset($config)) {
            if (array_key_exists('api_key', $config) === true) {
                $this->config->updateApiKey($config['api_key']);
            } elseif (array_key_exists('retries', $config)) {
                $this->config->updateRetries($config['retries']);
            } elseif (array_key_exists('timeout', $config)) {
                $this->config->updateTimeout($config['timeout']);
            }
        }

        return $this->address_service->validate($address);
    }

    public function validateAddresses(array $addresses): array
    {
        return $this->address_service->validateAddresses($addresses);
    }


    /**
     * Derive a User-Agent header from the environment.
     */
    private function deriveUserAgent(): string
    {
        $sdk_version = 'shipengine-php/' . self::VERSION;

        $os = explode(' ', php_uname());
        $os_kernel = $os[0] . '/' . $os[2];

        $php_version = 'PHP/' . phpversion();

        return $sdk_version . ' ' . $os_kernel . ' ' . $php_version;
    }
}
