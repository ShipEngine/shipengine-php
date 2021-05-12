<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

/**
 * `AddressValidateResult` Type to be returned by *AddressService*. This is the result of
 * validating a given address, whether it's valid or not.
 *
 * @package ShipEngine\Model\Address
 * @property bool $valid
 * @property Address|null $normalizedAddress
 * @property array $info
 * @property array $warnings
 * @property array $errors
 * @property string|null $requestId
 */
final class AddressValidateResult implements \JsonSerializable
{
    /**
     * Indicates whether the address is valid
     *
     * @var bool|null
     */
    public ?bool $isValid;

    /**
     * The normalized form of the address. This will only be populated if the
     * address was valid (i.e. `$valid` is `true`).
     *
     * Addresses are normalized according to the normalization rules of the
     * countryCode they're in.
     *
     * @var Address|null
     */
    public ?Address $normalizedAddress;

    /**
     * An array of informational messages about the address validation, such as minor corrections.
     *
     * @var array
     */
    public array $info = array();

    /**
     * Warning messages about the address validation, such as major changes that
     * were made to the normalized address.
     *
     * @var array
     */
    public array $warnings = array();

    /**
     * Error messages about the address validation, such as invalid fields that
     * prevent the address from being fully validated.
     *
     * @var array
     */
    public array $errors = array();

    /**
     * The unique ID that is associated with the current request to ShipEngine API
     * for address validation.
     *
     * @var string|null
     */
    public ?string $requestId;

    /**
     * AddressValidateResult Type constructor. Takes in a `POPO` (Plain Old PHP Object) that is
     * the response object from the `JSON-RPC` response we get back.
     *
     * @param array $apiResponse
     */
    public function __construct(array $apiResponse)
    {
        $result = $apiResponse['result'];
        $messages = $result['messages'];

        $this->isValid = $result['isValid'];

        if (isset($result['normalizedAddress'])) {
            $address = $result['normalizedAddress'];
            $this->normalizedAddress = new Address($address);
        } else {
            $this->normalizedAddress = null;
        }

        isset($apiResponse['id']) ?
            $this->requestId = $apiResponse['id'] :
            $this->requestId = null;

        foreach ($messages as $message) {
            if (isset($message['type'])) {
                switch ($message['type']) {
                    case 'error':
                        $this->errors[] = $message;
                        break;
                    case 'info':
                        $this->info[] = $message;
                        break;
                    case 'warning':
                        $this->warnings[] = $message;
                        break;
                }
            }
        }
    }

    /**
     * Return a JsonSerialized string representation of the `AddressValidateResult` Type.
     *
     * <code>
     * {
     * "valid": true,
     * "normalizedAddress": {
     * "address": {
     * "name": "BRUCE WAYNE",
     * "phone": "1234567891",
     * "company": "SHIPENGINE",
     * "street": [
     * "4 JERSEY ST"
     * ],
     * "cityLocality": "BOSTON",
     * "stateProvince": "MA",
     * "postalCode": "02215",
     * "countryCode": "US",
     * "isResidential": false
     * }
     * },
     * "info": [],
     * "warnings": [],
     * "errors": [],
     * "requestId": "req_9yvuxhYGymTzNorcM16gwT"
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
            'isValid' => $this->isValid,
            'normalizedAddress' => $this->normalizedAddress,
            'info' => $this->info,
            'warnings' => $this->warnings,
            'errors' => $this->errors,
            'requestId' => $this->requestId,
        ];
    }
}
