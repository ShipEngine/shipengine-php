<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Constants\Endpoints;

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
     * Validate an individual address or multiple addresses via the `addresses/validate` endpoint.
     *
     * <br>
     * **Usage**:
     * ```php
     * $addressService = new AddressService();
     * $addressService->validate(Address, ShipEngineConfig);
     * ```
     *
     * @param Address|array $address
     * @param ShipEngineConfig $config
     */
    public function validate($address, ShipEngineConfig $config)
    {
        if (!is_array($address)) {
            $request_body = [$address];
        } else {
            $request_body = $address;
        }

        $client = new ShipEngineClient();
        $api_response = $client->restRequest(
            'POST',
            Endpoints::VALIDATE_ADDRESS,
            $request_body,
            $config
        );

        $result = array();
        foreach ($api_response as $returned_address) {
            $result[] = new AddressValidateResult($returned_address);
        }

        if (count($result) === 1) {
            return $result[0];
        }
        return $result;
    }
}
