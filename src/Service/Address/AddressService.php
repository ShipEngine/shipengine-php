<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Service\AbstractService;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Validate a single address or multiple addresses.
 *
 * @package ShipEngine\Service\Address
 */
final class AddressService extends AbstractService
{
    /**
     * Validate a single address via the `address/validate` remote procedure.
     *
     * @param Address $params
     * @return AddressValidateResult
     */
    public function validate(Address $params): AddressValidateResult
    {
        $serializer = new ShipEngineSerializer();
        $response = $this->request('address/validate', (array)$params->jsonSerialize());

        $status_code = $response->getStatusCode();
        $reason_phrase = $response->getReasonPhrase();

        if ($status_code !== 200) {
            throw new ShipEngineException(
                "Address Validation request failed -- status_code: {$status_code} reason: {$reason_phrase}"
            );
        }

        $parsed_response = json_decode($response->getBody()->getContents());


//        if (count($parsed_response['error']) > 0) {
//            $errors = $parsed_response['error'];
//            throw new ShipEngineServerException(
//                $errors['message'],
//                $parsed_response['id'],
//                $errors['data']['error_source'],
//                $errors['data']['error_type'],
//                $errors['data']['error_code']
//            );
//        }

        return $serializer->deserializeJsonToType(
            json_encode($parsed_response->result),
            AddressValidateResult::class
        );
    }

    /**
     * Validate multiple addresses by passing in an array of `AddressValidateParams`.
     *
     * @param array $params
     * @return array An array of *AddressValidateResult* objects.
     */
    public function validateAddresses(array $params): array
    {
        $serializer = new ShipEngineSerializer();

        foreach ($params as &$rpcRequest) {
            $rpcRequest = $serializer->serializeDataToType($rpcRequest, AddressValidateResult::class);
        }

        $response = $this->batchRequest('address/validate', $params);
        $status_code = $response->getStatusCode();
        $reason_phrase = $response->getReasonPhrase();

        if ($response->getStatusCode() !== 200) {
            throw new ShipEngineException(
                "Validation request failed -- status_code: {$status_code} reason: {$reason_phrase}"
            );
        }

        $parsed_response = json_decode($response->getBody()->getContents());

        $result_array = array();

        foreach ($parsed_response as &$validated_address) {
            $validated_address = $serializer->deserializeJsonToType(
                json_encode($validated_address->result),
                AddressValidateResult::class
            );

            array_push($result_array, $validated_address);
        }

        return $result_array;
    }
}
