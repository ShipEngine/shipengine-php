<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\ShipEngineClient;
use ShipEngine\Util\Constants\RPCMethods;
use ShipEngine\Util\ShipEngineSerializer;

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
     * @param Address $params
     * @param ShipEngineConfig $config
     * @return AddressValidateResult
     * @throws ClientExceptionInterface
     */
    public function validate(Address $params, ShipEngineConfig $config): AddressValidateResult
    {
        $client = new ShipEngineClient();
        $serializer = new ShipEngineSerializer();
        $response = $client->request(
            RPCMethods::ADDRESS_VALIDATE,
            $params->jsonSerialize(),
            $config
        );

        return $serializer->deserializeJsonToType(
            json_encode($response),
            AddressValidateResult::class
        );
    }
}
