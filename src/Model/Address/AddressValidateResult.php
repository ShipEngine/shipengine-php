<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Util;

/**
 * `AddressValidateResult` Type to be returned by *AddressService*. This is the result of
 * validating a given address, whether it's valid or not.
 *
 * @package ShipEngine\Model\Address
 * @property bool $valid
 * @property Address|null $normalized_address
 * @property array $info
 * @property array $warnings
 * @property array $errors
 * @property string|null $request_id
 */
final class AddressValidateResult implements \JsonSerializable
{
    use Util\Getters;

    /**
     * Indicates whether the address is valid
     *
     * @var bool
     */
    public bool $valid;

    /**
     * The normalized form of the address. This will only be populated if the
     * address was valid (i.e. `$valid` is `true`).
     *
     * Addresses are normalized according to the normalization rules of the
     * country they're in.
     *
     * @var Address|null
     */
    public ?Address $normalized_address;

    /**
     * An array of informational messages about the address validation, such as minor corrections.
     *
     * @var array
     */
    public array $info;

    /**
     * Warning messages about the address validation, such as major changes that
     * were made to the normalized address.
     *
     * @var array
     */
    public array $warnings;

    /**
     * Error messages about the address validation, such as invalid fields that
     * prevent the address from being fully validated.
     *
     * @var array
     */
    public array $errors;

    /**
     * The unique ID that is associated with the current request to ShipEngine API
     * for address validation.
     *
     * @var string|null
     */
    public ?string $request_id;

    /**
     * AddressValidateResult Type constructor. Takes in a `POPO` (Plain Old PHP Object) that is
     * the response object from the `JSON-RPC` response we get back.
     *
     * @param array $api_response
     */
    public function __construct(array $api_response)
    {
        $result = $api_response['result'];
        $messages = $result['messages'];

        $this->valid = $result['valid'];

        if (isset($result['address'])) {
            $address = $result['address'];
            $this->normalized_address = new Address($address);
        } else {
            $this->normalized_address = null;
        }

        isset($api_response['id']) ?
            $this->request_id = $api_response['id'] :
            $this->request_id = null;

        $this->info = $messages['info'] ?? array();
        $this->warnings = $messages['warnings'] ?? array();
        $this->errors = $messages['errors'] ?? array();
    }

    /**
     * Return a JsonSerialized string representation of the `AddressValidateResult` Type.
     *
     * <code>
     * {
     * "valid": true,
     * "normalized_address": {
     * "address": {
     * "name": "BRUCE WAYNE",
     * "phone": "1234567891",
     * "company_name": "SHIPENGINE",
     * "street": [
     * "4 JERSEY ST"
     * ],
     * "city_locality": "BOSTON",
     * "state_province": "MA",
     * "postal_code": "02215",
     * "country_code": "US",
     * "residential": false
     * }
     * },
     * "info": [],
     * "warnings": [],
     * "errors": [],
     * "request_id": "req_9yvuxhYGymTzNorcM16gwT"
     * }
     * <code>
     *
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>
     */
    public function jsonSerialize()
    {
        return [
            'valid' => $this->valid,
            'normalized_address' => $this->normalized_address,
            'info' => $this->info,
            'warnings' => $this->warnings,
            'errors' => $this->errors,
            'request_id' => $this->request_id,
        ];
    }
}
