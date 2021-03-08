<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Model\Address\Address;

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
        $parameters = array(
            $street,
            $city,
            $state,
            $postal_code,
            $country_code,
            $residential
        );

//      NOTE: For now this just returns the string that is the city of
//      the address passed in IF the address was validated.
        return $this->addresses->validate($parameters);
    }
}
