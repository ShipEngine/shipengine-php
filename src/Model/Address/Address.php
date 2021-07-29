<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Message\ValidationException;
use ShipEngine\Util\Assert;

/**
 * `Address` Type to be passed into the *validateAddress* method
 * and internal **AddressService**.
 *
 * @package ShipEngine\Model\Address
 * @property array $street
 * @property string|null $cityLocality
 * @property string|null $stateProvince
 * @property string|null $postalCode
 * @property string $countryCode
 * @property bool|null $isResidential
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $company
 * @throws ValidationException
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
    public ?string $cityLocality;

    /**
     * The state or province.
     *
     * @var string|null
     */
    public ?string $stateProvince;

    /**
     * The postal code or zip code.
     *
     * @var string|null
     */
    public ?string $postalCode;

    /**
     * The ISO 3166 countryCode code
     *
     * @link https://en.wikipedia.org/wiki/List_of_ISO_3166_countryCodes
     * @var string
     */
    public string $countryCode;

    /**
     * Indicates whether the address is isResidential or commercial, if known.
     *
     * @var bool|null
     */
    public ?bool $isResidential;

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
        $this->isResidential = $address['isResidential'] ?? null;
        $this->name = $address['name'] ?? '';
        $this->phone = $address['phone'] ?? '';
        $this->company = $address['company'] ?? '';
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
     *  "cityLocality": "Boston",
     *  "stateProvince": "MA",
     *  "postalCode": "02215",
     *  "countryCode": "US"
     * }
     * </code>
     */
    public function jsonSerialize()
    {
        return [
            'address' => [
                'name' => $this->name,
                'phone' => $this->phone,
                'company' => $this->company,
                'street' => $this->street,
                'cityLocality' => $this->cityLocality,
                'stateProvince' => $this->stateProvince,
                'postalCode' => $this->postalCode,
                'countryCode' => $this->countryCode,
                'isResidential' => $this->isResidential
            ]
        ];
    }
}
