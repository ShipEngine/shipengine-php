<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Message\ShipEngineValidationException;
use ShipEngine\Util;

/**
 * `Address` Type to be passed into the *validateAddress* method
 * and internal **AddressService**.
 *
 * @throws ShipEngineValidationException
 *@package ShipEngine\Model\Address
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
final class Address implements \JsonSerializable
{
    use Util\Getters;

    /**
     * @var array
     */
    private array $street;

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
     * @var string
     */
    private string $country_code;

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
     * Address Type constructor.
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
        if (!empty($street)) {
            $this->street = $street;
        } else {
            throw new ShipEngineValidationException(
                'Invalid address. At least one address line is required.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        }

        if (count($street) > 3) {
            throw new ShipEngineValidationException(
                'Invalid address. No more than 3 street lines are allowed.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        } else {
            $this->street = $street;
        }

        if (preg_match('/^[a-zA-Z0-9\s\W]*$/', $city_locality) === false || $city_locality === '') {
            throw new ShipEngineValidationException(
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
            throw new ShipEngineValidationException(
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
            throw new ShipEngineValidationException(
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
            throw new ShipEngineValidationException(
                "Invalid address. The country must be specified.",
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        } elseif (!preg_match('/^[A-Z]{2}$/', $country_code)) {
            throw new ShipEngineValidationException(
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
        $this->company_name = $company_name;
    }

    /**
     * Return a JsonSerialized string representation of the `Address` Type.
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
            'name' => $this->name,
            'phone' => $this->phone,
            'company_name' => $this->company_name,
            'street' => $this->street,
            'city_locality' => $this->city_locality,
            'state_province' => $this->state_province,
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code,
            'residential' => $this->residential
        ];
    }
}
