<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Util;

/**
 * `AddressValidateParams` Type to be passed into the *AddressService*.
 *
 * @package ShipEngine\Model\Address
 * @property array $street
 * @property string $country_code
 * @property string|null $city_locality
 * @property string|null $state_province
 * @property string|null $postal_code
 * @property bool|null $residential
 */
final class AddressValidateParams
{
    use Util\Getters;

    /**
     * @var array
     */
    private array $street;

    /**
     * @var string
     */
    private string $country_code;

    /**
     * @var string|null
     */
    private ?string $city_locality;

    /**
     * @var string|null
     */
    private ?string $state_province;

    /**
     * @var string|null
     */
    private ?string $postal_code;

    /**
     * @var bool|null
     */
    private ?bool $residential;

    /**
     * AddressValidateParams Type constructor.
     * @param array $street
     * @param string $country_code
     * @param string|null $city_locality
     * @param string|null $state_province
     * @param string|null $postal_code
     * @param bool|null $residential
     */
    public function __construct(
        array $street,
        string $country_code,
        ?string $city_locality = null,
        ?string $state_province = null,
        ?string $postal_code = null,
        ?bool $residential = null
    ) {
        $this->street = $street;
        $this->city_locality = $city_locality;
        $this->state_province = $state_province;
        $this->postal_code = $postal_code;
        $this->country_code = $country_code; // TODO: Add validation to enforce 2 char country_codes
        $this->residential = $residential;
    }

    /**
     * Return a JsonSerialized string representation of the `AddressValidateParams` Type.
     *
     * Output Example:
     * ```json
     * {
     * "street": [
     * "4 Jersey St",
     * "ste 200"
     * ],
     * "city_locality": "Boston",
     * "state_province": "MA",
     * "postal_code": "02215",
     * "country_code": "US"
     * }
     * ```
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode([
            'street' => $this->street,
            'city_locality' => $this->city_locality,
            'state_province' => $this->state_province,
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code,
            'residential' => $this->residential
        ], JSON_PRETTY_PRINT);
    }
}
