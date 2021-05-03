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
 * @property bool|null $residential
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $company
 */
final class Address implements \JsonSerializable
{
    /**
     * The street address. If the street address is multiple lines, then pass an
     * array of lines (up to 3).
     *
     * @var array
     */
    public array $street;

    /**
     * The city or locality.
     *
     * @var string|null
     */
    public ?string $city_locality;

    /**
     * The state or province.
     *
     * @var string|null
     */
    public ?string $state_province;

    /**
     * The postal code or zip code.
     *
     * @var string|null
     */
    public ?string $postal_code;

    /**
     * The ISO 3166 country code
     *
     * @link https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
     * @var string
     */
    public string $country_code;

    /**
     * Indicates whether the address is residential or commercial, if known.
     *
     * @var bool|null
     */
    public ?bool $residential;

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

        $this->residential = $address['residential'] ?? null;
        $this->name = $address['name'] ?? '';
        $this->phone = $address['phone'] ?? '';
        $this->company = $address['company_name'] ?? '';
    }

    /**
     * Assertions to validate that the address array items are  in the type/format we need them to be.
     *
     * @param array $address
     */
    public function validateInput(array $address): void
    {
        $assert = new Assert();

        $assert->isStreetSet($address['street']);
        $assert->tooManyAddressLines($address['street']);
        $this->street = $address['street'];

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
     *  "company_name": "ShipEngine",
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
            'address' => [
                'name' => $this->name,
                'phone' => $this->phone,
                'company_name' => $this->company,
                'street' => $this->street,
                'city_locality' => $this->city_locality,
                'state_province' => $this->state_province,
                'postal_code' => $this->postal_code,
                'country_code' => $this->country_code,
                'residential' => $this->residential
            ]
        ];
    }
}
