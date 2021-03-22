<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Util;

/**
 * `AddressValidateParams` Type to be passed into the *AddressService*.
 *
 * @package ShipEngine\Model\Address
 * @property array $street
 * @property string|null $city_locality
 * @property string|null $state_province
 * @property string|null $postal_code
 * @property string $country_code
 * @property bool|null $residential
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $company_name
 */
final class AddressValidateParams implements \JsonSerializable
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
     * @var string|null
     */
    private ?string $name;

    /**
     * @var string|null
     */
    private ?string $phone;

    /**
     * @var string|null
     */
    private ?string $company_name;

    /**
     * AddressValidateParams Type constructor.
     *
     * @param array $street
     * @param string|null $city_locality
     * @param string|null $state_province
     * @param string|null $postal_code
     * @param string $country_code
     * @param bool|null $residential
     * @param string|null $name
     * @param string|null $phone
     * @param string|null $company_name
     */
    public function __construct(
        array $street,
        ?string $city_locality,
        ?string $state_province,
        ?string $postal_code,
        string $country_code,
        ?bool $residential = null,
        ?string $name = null,
        ?string $phone = null,
        ?string $company_name = null
    ) {
        $this->street = $street;
        $this->city_locality = $city_locality;
        $this->state_province = $state_province;
        $this->postal_code = $postal_code;
        $this->country_code = $country_code;
        $this->residential = $residential;
        $this->name = $name;
        $this->phone = $phone;
        $this->company_name = $company_name;
    }

    /**
     * Return a JsonSerialized string representation of the `AddressValidateParams` Type.
     *
     * Output Example:
     * ```json
     * {
     * "name": "ShipEngine",
     * "phone": "123465798",
     * "company_name": "ShipEngine",
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
            'name' => $this->name,
            'phone' => $this->phone,
            'company_name' => $this->company_name,
            'street' => $this->street,
            'city_locality' => $this->city_locality,
            'state_province' => $this->state_province,
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code,
            'residential' => $this->residential
        ]);
    }
}
