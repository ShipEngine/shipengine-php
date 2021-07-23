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
    public string $status;

    public Address $original_address;

    public Address $matched_address;

    public array $messages = array();

    /**
     * AddressValidateResult Type constructor. Takes in a `POPO` (Plain Old PHP Object) that is
     * the response object from the API response we get back from ShipEngine.
     *
     * @param array $api_response
     */
    public function __construct(array $api_response)
    {
        $incoming_messages = $api_response['messages'];

        $this->status = $api_response['status'];
        $this->original_address = new Address($api_response['original_address']);
        $this->matched_address = new Address($api_response['matched_address']);


        foreach ($incoming_messages as $message) {
            $this->messages[] = new AddressMessage($message);
        }
    }

    /**
     * Return a JsonSerialized string representation of the `AddressValidateResult` Type.
     *
     * <code>
     * {
     * "status": "verified",
     * "original_address": [
     * {
     * "name": "ShipEngine",
     * "phone": "1-234-567-8910",
     * "company": "",
     * "address_line1": "3800 N Lamar Blvd.",
     * "address_line2": "ste 220",
     * "address_line3": "",
     * "city_locality": "Austin",
     * "state_province": "TX",
     * "postal_code": "78756",
     * "country_code": "US",
     * "address_residential_indicator": "no"
     * }
     * ],
     * "matched_address": [
     * {
     * "name": "SHIPENGINE",
     * "phone": "1-234-567-8910",
     * "company": "",
     * "address_line1": "3800 N LAMAR BLVD STE 220",
     * "address_line2": "",
     * "address_line3": "",
     * "city_locality": "AUSTIN",
     * "state_province": "TX",
     * "postal_code": "78756-0003",
     * "country_code": "US",
     * "address_residential_indicator": "no"
     * }
     * ],
     * "messages": []
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
            'status' => $this->status,
            'original_address' => $this->original_address,
            'matched_address' => $this->matched_address,
            'messages' => $this->messages,
        ];
    }
}
