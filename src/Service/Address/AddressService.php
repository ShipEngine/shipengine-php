<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Assert;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * Validate a single address or multiple addresses.
 *
 * <br>
 * **Usage**:
 * ```php
 * $addressService = new AddressService();
 * $addressService->validate(args);
 * $addressService->normalize(args);
 * ```
 * @package ShipEngine\Service\Address
 */
final class AddressService
{
    /**
     * Validate a single address via the `address/validate` remote procedure.
     *
     * <br>
     * **Usage**:
     * ```php
     * $addressService = new AddressService();
     * $addressService->validate(Address, ShipEngineConfig);
     * ```
     *
     * @param Address $address
     * @param ShipEngineConfig $config
     * @return AddressValidateResult
     * @throws ClientExceptionInterface
     */
    // public function validate(Address $address, ShipEngineConfig $config): AddressValidateResult
    // {
    //     $client = new ShipEngineClient();
    //     $apiResponse = $client->request(
    //         RPCMethods::ADDRESS_VALIDATE,
    //         $config,
    //         $address->jsonSerialize()
    //     );

    //     return new AddressValidateResult($apiResponse);
    // }

    /**
     * Normalize a given address into a standardized format.
     *
     * <br>
     * **Usage**:
     * ```php
     * $addressService = new AddressService();
     * $addressService->normalize(Address, ShipEngineConfig);
     * ```
     *
     * @param Address $address
     * @param ShipEngineConfig $config
     * @return Address
     * @throws ClientExceptionInterface
     */
    // public function normalize(Address $address, ShipEngineConfig $config): Address
    // {
    //     $assert = new Assert();
    //     $validationResult = $this->validate($address, $config);
    //     $assert->doesNormalizedAddressHaveErrors($validationResult);
    //     return $validationResult->normalizedAddress;
    // }
}
