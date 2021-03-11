<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateParams;
use ShipEngine\ShipEngineError;

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
     */
    public function validateAddress(
        array $street,
        string $city,
        string $state,
        string $postal_code,
        string $country_code,
        ?bool $residential = null
    ): Address {

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
            $errors = $result->messages['errors'][0];
            $error_string = '';
            foreach ($errors as $error) { // TODO: FIX CODE BREAKING HERE.
                $error_string += $error;
            }
            throw new ShipEngineError($error_string);
        }

        if ($result->address != null) {
            $address = $result->address;
            return new Address(
                $address->valid,
                $result->messages,
                $address->street,
                $address->city_locality,
                $address->state_province,
                $address->postal_code,
                $address->country_code,
                $address->residential
            );
        }
    }
//    TODO: Need to look into how to specify a type of an array of type Address
//    public function validateAddresses(Address $addresses): Address
//    {
//
//    }
}
