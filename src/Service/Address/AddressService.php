<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

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
     * Service HTTP requests to ShipEngine API.
     *
     * @var ShipEngineClient
     */
    private ShipEngineClient $client;
    // TODO: refactor to new up a new SE Client for each request -- start in validate()

    /**
     * AddressService constructor - this takes in the instance of the `ShipEngineClient` that
     * the `ShipEngine` Class is using.
     *
     * @param ShipEngineClient $client
     */
    public function __construct(ShipEngineClient $client)
    {
        $this->client = $client;
    }

    /**
     * Validate a single address via the `address/validate` remote procedure.
     *
     * @param Address $params
     * @param ShipEngineConfig $config
     * @return AddressValidateResult
     */
    public function validate(Address $params, ShipEngineConfig $config): AddressValidateResult
    {
        $serializer = new ShipEngineSerializer();
        $response = $this->client->request(
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
