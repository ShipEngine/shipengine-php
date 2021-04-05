<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Service\AbstractService;
use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Validate a single address or multiple addresses.
 *
 * @package ShipEngine\Service\Address
 */
final class AddressService extends AbstractService
{
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
        $response = $this->request('address/validate', (array)$params->jsonSerialize(), $config);


        return $serializer->deserializeJsonToType(
            json_encode($response),
            AddressValidateResult::class
        );
    }
}
