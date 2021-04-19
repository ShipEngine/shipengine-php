<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Util;

/**
 * `AddressValidateResult` Type to be returned by *AddressService*.
 *
 * @package ShipEngine\Model\Address
 * @property bool $valid
 * @property array $messages
 * @property array|null $normalized_address
 */
final class AddressValidateResult implements \JsonSerializable
{
    use Util\Getters;

    /**
     * @var bool
     */
    private bool $valid;

    /**
     * @var array|null
     */
    private ?array $normalized_address;

    private array $info;

    private array $warnings;

    private array $errors;


    /**
     * @var array
     */
    private array $messages;

    /**
     * AddressValidateResult Type constructor.
     *
     * @param bool $valid
     * @param array|null $normalized_address
     * @param array $info
     * @param array $warnings
     * @param array $errors
     */
    public function __construct(
        bool $valid,
        ?array $normalized_address = null,
        array $info = array(),
        array $warnings = array(),
        array $errors = array()
    ) {
        $this->valid = $valid;
        $this->normalized_address = $normalized_address;
        $this->info = $info;
        $this->warnings = $warnings;
        $this->errors = $errors;
    }

    /**
     * Return a JsonSerialized string representation of the `AddressValidateResult` Type.
     *
     * <code>
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
     * "info": [
     * "Duis",
     * "voluptate sed sunt",
     * "nisi irure amet",
     * "dolore aute",
     * "exercitation esse aliquip aute est"
     * ],
     * "warnings": [],
     * "errors": [
     * "aute ea nulla",
     * "occaecat consequat consectetur in esse",
     * "aliqua sed"
     * ]
     * }
     * <code>
     */
    public function jsonSerialize()
    {
        return [
            'valid' => $this->valid,
            'normalized_address' => $this->normalized_address,
            'info' => $this->info,
            'warnings' => $this->warnings,
            'errors' => $this->errors
        ];
    }
}
