<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Message\ValidationException;
use ShipEngine\Util;

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
     * @see https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
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
        if (!empty($street)) {
            $this->street = $street;
        } else {
            throw new ValidationException(
                'Invalid address. At least one address line is required.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        }

        if (count($street) > 3) {
            throw new ValidationException(
                'Invalid address. No more than 3 street lines are allowed.',
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        } else {
            $this->street = $street;
        }

        if (preg_match('/^[a-zA-Z0-9\s\W]*$/', $city_locality) === false || $city_locality === '') {
            throw new ValidationException(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        } else {
            $this->city_locality = $city_locality;
        }

        if (preg_match('/^[A-Z\W]{2}$/', $state_province) === false || $state_province === '') {
            throw new ValidationException(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        } else {
            $this->state_province = $state_province;
        }

        if (preg_match('/^[a-zA-Z0-9\s-]*$/', $postal_code) === false || $postal_code == '') {
            throw new ValidationException(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        } else {
            $this->postal_code = $postal_code;
        }


        if (preg_match('/^[A-Z]{2}$/', $country_code)) {
            $this->country_code = $country_code;
        } elseif ($country_code == '') {
            throw new ValidationException(
                "Invalid address. The country must be specified.",
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        } elseif (!preg_match('/^[A-Z]{2}$/', $country_code)) {
            throw new ValidationException(
                "Invalid address. {$country_code} is not a valid country code.",
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        }

        $this->residential = $residential;
        $this->name = $name;
        $this->phone = $phone;
        $this->company = $company;
    }

    /**
     * Returns an array that can be easily JsonSerialized using `json_encode()` which will
     * yield a JSON string representation of the `Address` Type.
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
