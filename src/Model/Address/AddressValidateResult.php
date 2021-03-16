<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Util;

/**
 * `AddressValidateResult` Type to be returned by *AddressService*.
 *
 * @package ShipEngine\Model\Address
 * @property bool $valid
 * @property array $messages
 * @property array|null $address
 */
final class AddressValidateResult implements \JsonSerializable
{
    use Util\Getters;

    /**
     * @var bool
     */
    private bool $valid;

    /**
     * @var array
     */
    private array $messages;


    /**
     * @var array|null
     */
    private ?array $address;

    /**
     * AddressValidateResult Type constructor.
     *
     * @param bool $valid
     * @param array $messages
     * @param array|null $address
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
     * Return a JsonSerialized string representation of the `AddressValidateResult` Type.
     *
     * ```json
     * {
     * "valid": true,
     * "address": {
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
