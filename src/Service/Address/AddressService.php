<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\ShipEngineClient;
use ShipEngine\Util\Assert;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * Validate a single address or multiple addresses.
 *
 * @package ShipEngine\Service\Address
 */
final class AddressService
{
    /**
     * Validate a single address via the `address/validate` remote procedure.
     *
     * @param Address $address
     * @param ShipEngineConfig $config
     * @return AddressValidateResult
     * @throws ClientExceptionInterface
     */
    public function validate(Address $address, ShipEngineConfig $config): AddressValidateResult
    {
        $client = new ShipEngineClient();
        $response = $client->request(
            RPCMethods::ADDRESS_VALIDATE,
            $address->jsonSerialize(),
            $config
        );

        return new AddressValidateResult($response);
    }

    /**
     * Normalize a given address into a standardized format.
     *
     * @param Address $address
     * @param ShipEngineConfig $config
     * @return Address
     * @throws ClientExceptionInterface
     */
    public function normalize(Address $address, ShipEngineConfig $config): Address
    {
        $assert = new Assert();
        $response = $this->validate($address, $config);
        $assert->doesNormalizedAddressHaveErrors($response);
        return $response->normalized_address;
    }
}
