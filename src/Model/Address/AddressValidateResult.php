<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Util;

/**
 * `AddressValidateResult` Type to be returned by *AddressService*.
 *
 * @package ShipEngine\Model\Address
 * @property array $result_array
// * @property bool $valid
// * @property array $messages
// * @property Address|null $normalized_address
 */
final class AddressValidateResult implements \JsonSerializable
{
    use Util\Getters;

    /**
     * @var bool
     */
    public bool $valid;

    /**
     * @var Address|null
     */
    public ?Address $normalized_address;

    public array $info;

    public array $warnings;

    public array $errors;


    /**
     * @var array
     */
    public array $messages;

    /**
     * AddressValidateResult Type constructor.
     *
     * @param array $result_array
     */
    // TODO: 1.) refactor to accept POPO - and add request_id and new up Address() on $this->normalized_address
    public function __construct(
        array $result_array // FIXME: 2.) now accepting a POPO just need to document it in the docstring
    ) {
        $this->valid = $result_array['valid'];

        isset($result_array['address']) ?
            $this->normalized_address = new Address(
                $result_array['address']['street'],
                $result_array['address']['city_locality'],
                $result_array['address']['state_province'],
                $result_array['address']['postal_code'],
                $result_array['address']['country_code'],
                $result_array['address']['residential'],
                $result_array['address']['name'] ?? '',
                $result_array['address']['phone'] ?? '',
                $result_array['address']['company_name'] ?? '',
            ) : $this->normalized_address = null;


        $this->info = $result_array['messages']['info'] ?? array();
        $this->warnings = $result_array['messages']['warnings'] ?? array();
        $this->errors = $result_array['messages']['errors'] ?? array();
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
