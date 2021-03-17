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
     * "name": "ShipEngine",
     * "phone": "1234567891",
     * "company_name": "ShipEngine",
     * "street": [
     * "in nostrud consequat nisi"
     * ],
     * "country_code": "BK",
     * "postal_code": "ullamco culpa",
     * "city_locality": "aliqua",
     * "residential": false
     * },
     * "messages": {
     * "errors": [
     * "aute ea nulla",
     * "occaecat consequat consectetur in esse",
     * "aliqua sed"
     * ],
     * "info": [
     * "Duis",
     * "voluptate sed sunt",
     * "nisi irure amet",
     * "dolore aute",
     * "exercitation esse aliquip aute est"
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
