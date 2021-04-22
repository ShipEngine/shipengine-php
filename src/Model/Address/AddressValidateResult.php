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

    public ?string $request_id;


    /**
     * @var array
     */
    public array $messages;

    /**
     * AddressValidateResult Type constructor. Takes in a `POPO` that contains
     * the result object from the `JSON-RPC` response we get back.
     *
     * @param array $result_array
     */
    // TODO: 1.) refactor to accept POPO - and add request_id and new up Address() on $this->normalized_address
    public function __construct(
        array $result_array
    ) {
        $this->valid = $result_array['result']['valid'];

        if (isset($result_array['result']['address'])) {
            $address = $result_array['result']['address'];
            $this->normalized_address = new Address(
                $address['street'],
                $address['city_locality'],
                $address['state_province'],
                $address['postal_code'],
                $address['country_code'],
                $address['residential'],
                $address['name'] ?? '',
                $address['phone'] ?? '',
                $address['company_name'] ?? '',
            );
        } else {
            $this->normalized_address = null;
        }

        isset($result_array['id']) ?
            $this->request_id = $result_array['id'] :
            $this->request_id = null;

        $this->info = $result_array['result']['messages']['info'] ?? array();
        $this->warnings = $result_array['result']['messages']['warnings'] ?? array();
        $this->errors = $result_array['result']['messages']['errors'] ?? array();
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
