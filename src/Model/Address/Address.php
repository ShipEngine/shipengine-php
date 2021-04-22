<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Message\ValidationException;
use ShipEngine\Util;
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
    use Util\Getters;

    /**
     * The street address. If the street address is multiple lines, then pass an
     * array of lines (up to 3).
     *
     * @var array
     */
    private array $street;

    /**
     * The city or locality.
     *
     * @var string|null
     */
    private ?string $city_locality;

    /**
     * The state or province.
     *
     * @var string|null
     */
    private ?string $state_province;

    /**
     * The postal code or zip code.
     *
     * @var string|null
     */
    private ?string $postal_code;

    /**
     * The ISO 3166 country code
     *
     * @link https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
     * @var string
     */
    private string $country_code;

    /**
     * Indicates whether the address is residential or commercial, if known.
     *
     * @var bool|null
     */
    private ?bool $residential;

    /**
     * The name of the sender or recipient at the address, if applicable.
     *
     * @var string|null
     */
    private ?string $name;

    /**
     * The phone number associated with this address, if any.
     *
     * @var string|null
     */
    private ?string $phone;

    /**
     * The company name, if this is a business address.
     *
     * @var string|null
     */
    private ?string $company;

    /**
     * Address Type constructor. This object is used in the AddressService
     * methods as the $params object that gets passed in.
     *
     * @param array $street
     * @param string|null $city_locality
     * @param string|null $state_province
     * @param string|null $postal_code
     * @param string $country_code
     * @param bool|null $residential
     * @param string|null $name
     * @param string|null $phone
     * @param string|null $company
     */
    // TODO: refactor to accept an array instead of individual : POPO
    public function __construct(
        array $street,
        ?string $city_locality,
        ?string $state_province,
        ?string $postal_code,
        string $country_code,
        ?bool $residential = null,
        ?string $name = '',
        ?string $phone = '',
        ?string $company = ''
    ) {
        $this->validateInput($street, $city_locality, $state_province, $postal_code, $country_code);

        $this->residential = $residential;
        $this->name = $name;
        $this->phone = $phone;
        $this->company = $company;
    }

    public function validateInput(
        array $street,
        ?string $city_locality,
        ?string $state_province,
        ?string $postal_code,
        string $country_code
    ): void {
        $assert = new Assert();

        $assert->isStreetSet($street);
        $assert->tooManyAddressLines($street);
        $this->street = $street;

        $assert->isCityValid($city_locality);
        $this->city_locality = $city_locality;

        $assert->isStateValid($state_province);
        $this->state_province = $state_province;

        $assert->isPostalCodeValid($postal_code);
        $this->postal_code = $postal_code;

        $assert->isCountryCodeValid($country_code);
        $this->country_code = $country_code;
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
