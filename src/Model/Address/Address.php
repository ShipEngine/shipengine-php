<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Util;

/**
 * `Address` Type to be returned by the *validateAddress()* method in the *AddressTrait*.
 *
 * @package ShipEngine\Model\Address
 * @property bool $valid
 * @property array|null $address
 * @property array $messages
 */
final class Address implements \JsonSerializable
{
    use Util\Getters;

    /**
     * @var bool
     */
    private ?bool $valid;

    /**
     * @var array|null
     */
    private ?array $address;

    /**
     * @var array
     */
    private array $messages;

    /**
     * AddressValidateResult Type constructor.
     *
     * @param bool $valid
     * @param array|null $address
     * @param array $messages
     */
    public function __construct(
        bool $valid,
        array $messages,
        ?array $address
    ) {
        $this->valid = $valid;
        $this->address = $address;
        $this->messages = $messages;
    }

    /**
     * Return a JsonSerialized string representation of the `Address` Type.
     *
     * ```json
     * {
     * "valid": true,
     * "address": {
     * "street": [
     * "4 Jersey St"
     * ],
     * "city_locality": "Boston",
     * "state_province": "MA",
     * "postal_code": "02215",
     * "country_code": "US",
     * "residential": false
     * },
     * "messages": {
     * "info": [],
     * "errors": [],
     * "warnings": [
     * "There was a change or addition to the state/province."
     * ]
     * }
     * }
     * ```
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode([
            'valid' => $this->valid,
            'address' => $this->address,
            'messages' => $this->messages
        ], JSON_PRETTY_PRINT);
    }
}
