<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Message\ShipEngineError;
use ShipEngine\Model\Address\AddressValidateParams;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Service\AbstractService;
use ShipEngine\Util\ShipEngineSerializer;

final class AddressService extends AbstractService
{
    /**
     * Validate a single address via the `address/validate` remote procedure.
     *
     * @param AddressValidateParams $params
     * @return AddressValidateResult
     */
    public function validate(AddressValidateParams $params): AddressValidateResult
    {
        $serializer = new ShipEngineSerializer();
        $response = $this->request('address/validate', (array)$params);
        $status_code = $response->getStatusCode();
        $reason_phrase = $response->getReasonPhrase();

        if ($status_code != 200) {
            throw new ShipEngineError(
                "Validation request failed -- status_code: {$status_code} reason: {$reason_phrase}"
            );
        }

        $parsed_response = json_decode($response->getBody()->getContents());

        return $serializer->deserializeJsonToType(json_encode($parsed_response->result), AddressValidateResult::class);
    }

    public function validateAddresses(array $params): array
    {
        $serializer = new ShipEngineSerializer();
        $response = $this->batchRequest('address/validate', $params);
        $status_code = $response->getStatusCode();
        $reason_phrase = $response->getReasonPhrase();

        if ($response->getStatusCode() != 200) {
            throw new ShipEngineError(
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
