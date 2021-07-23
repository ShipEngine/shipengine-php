<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Message\ValidationException;
use ShipEngine\Util\Assert;

/**
 * `Address` Type to be passed into the *validateAddress* method
 * and internal **AddressService**.
 *
 * @throws ValidationException
 * @package ShipEngine\Model\Address
 * @property array $street
 * @property string|null $city_locality
 * @property string|null $state_province
 * @property string|null $postal_code
 * @property string $country_code
 * @property bool|null $address_residential_indicator
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $company
 */
final class Address implements \JsonSerializable
{
    public string $address_line1;

    public ?string $address_line2;

    public ?string $address_line3;

    /**
     * The city or locality.
     *
     * @var string
     */
    public string $city_locality;

    /**
     * The state or province.
     *
     * @var string
     */
    public string $state_province;

    /**
     * The postal code or zip code.
     *
     * @var string|null
     */
    public ?string $postal_code;

    /**
     * The ISO 3166 country_code code
     *
     * @link https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
     * @var string
     */
    public string $country_code;

    /**
     * Indicates whether the address is residential or commercial. Can be either
     * "yes", "no", or "unknown" (default).
     *
     * @var string|null
     */
    public ?string $address_residential_indicator;

    /**
     * The name of the sender or recipient at the address, if applicable.
     *
     * @var string|null
     */
    public ?string $name;

    /**
     * The phone number associated with this address, if any.
     *
     * @var string|null
     */
    public ?string $phone;

    /**
     * The company name, if this is a business address.
     *
     * @var string|null
     */
    public ?string $company;

    /**
     * Address Type constructor. This object is used in the AddressService
     * methods as the $params object that gets passed in.
     *
     * @param array $address
     */
    public function __construct(array $address)
    {
        $this->validateInput($address);

        $this->address_residential_indicator = $address['address_residential_indicator'] ?? 'unknown';
        $this->name = $address['name'] ?? '';
        $this->phone = $address['phone'] ?? '';
        $this->company = $address['company'] ?? '';
    }

    /**
     * Assertions to validate that the address array items are  in the type/format we need them to be.
     *
     * @param array $address
     */
    public function validateInput(array $address): void
    {
        $assert = new Assert();

        $this->address_line1 = $address['address_line1'];
        $this->address_line2 = $address['address_line2'] ?? '';
        $this->address_line3 = $address['address_line3'] ?? '';

        $assert->isCityValid($address['city_locality']);
        $this->city_locality = $address['city_locality'];

        $assert->isStateValid($address['state_province']);
        $this->state_province = $address['state_province'];

        $assert->isPostalCodeValid($address['postal_code']);
        $this->postal_code = $address['postal_code'];

        $assert->isCountryCodeValid($address['country_code']);
        $this->country_code = $address['country_code'];
    }

    /**
     * Returns an array that can be easily JsonSerialized using `json_encode()` which will
     * yield a JSON string representation of the `Address` Type.
     *
     * Output Example:
     * <code>
     * {
     *  "name": "ShipEngine",
     *  "phone": "123465798",
     *  "company": "ShipEngine",
     *  "street": [
     *      "4 Jersey St",
     *      "ste 200"
     *  ],
     *  "city_locality": "Boston",
     *  "state_province": "MA",
     *  "postal_code": "02215",
     *  "country_code": "US"
     * }
     * </code>
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'company' => $this->company,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'address_line3' => $this->address_line3,
            'city_locality' => $this->city_locality,
            'state_province' => $this->state_province,
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code,
            'address_residential_indicator' => $this->address_residential_indicator
        ];
    }
}
