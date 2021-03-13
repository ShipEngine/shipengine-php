<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Message\ShipEngineError;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateParams;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Convenience method to `validate` a single address.
 */
trait AddressTrait
{
    /**
     * A method to `validate` a single address via the *address/validate* remote procedure.
     *
     * @param array $street
     * @param string $city
     * @param string $state
     * @param string $postal_code
     * @param string $country_code
     * @param bool|null $residential
     * @return Address
     * @throws ShipEngineError
     */
    public function validateAddress(
        array $street,
        string $city,
        string $state,
        string $postal_code,
        string $country_code,
        ?bool $residential = null
    ): Address {

        $serializer = new ShipEngineSerializer();

        $address_validation_params = new AddressValidateParams(
            $street,
            $city,
            $state,
            $postal_code,
            $country_code,
            $residential
        );

        $result = $this->addresses->validate($address_validation_params);

        if ($result->valid == false) {
            $errors = $result->messages['errors'];
            $error_string = '';
            foreach ($errors as $error) { // TODO: FIX CODE BREAKING HERE.
                $error_string = $error;
            }
            throw new ShipEngineError($error_string);
        }

        return $serializer->deserializeJsonToType($result->jsonSerialize(), Address::class);
    }
}
