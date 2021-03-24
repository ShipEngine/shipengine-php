<?php

declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Message\ShipEngineError;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateParams;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Convenience method to `validate` a single address.
 *
 * @package ShipEgnine\Service\Address
 */
trait AddressTrait
{
    /**
     * A method to `validate` a single address via the *address/validate* remote procedure.
     *
     * @param array $street
     * @param string|null $city
     * @param string|null $state
     * @param string|null $postal_code
     * @param string $country_code
     * @param string|null $name
     * @param string|null $phone
     * @param string|null $company_name
     * @param bool|null $residential
     * @return Address
     * @throws \Symfony\Component\Serializer\Exception\NotEncodableValueException
     * @package ShipEngine\Service\Address
     */
    public function validateAddress(
        array $street,
        ?string $city,
        ?string $state,
        ?string $postal_code,
        string $country_code,
        ?bool $residential = null,
        ?string $name = null,
        ?string $phone = null,
        ?string $company_name = null
    ): Address {
        $serializer = new ShipEngineSerializer();

        $address_validation_params = new AddressValidateParams(
            $street,
            $city,
            $state,
            $postal_code,
            $country_code,
            $residential,
            $name,
            $phone,
            $company_name
        );

        $result = $this->addresses->validate($address_validation_params);

        $returnValue = $serializer->deserializeJsonToType(json_encode($result), Address::class);

        if ($returnValue->valid === false) {
            $errors = $returnValue->messages['errors'];
            $error_string = '';
            foreach ($errors as $error) {
                $error_string = $error;
            }
            throw new ShipEngineError($error_string);
        }

        return $returnValue;
    }
}
