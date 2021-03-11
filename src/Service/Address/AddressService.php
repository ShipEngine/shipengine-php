<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Message\ShipEngineError;
use ShipEngine\Message\ShipEngineErrorMessage;
use ShipEngine\Message\ShipEngineInfo;
use ShipEngine\Message\ShipEngineWarning;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateParams;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Service\AbstractService;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class AddressService extends AbstractService
{
    /**
     * Validate a single address via the `address/validate` remote procedure.
     *
     * @param AddressValidateParams $params
     * @return AddressValidateResult
     */
    public function validate(AddressValidateParams $params)
    {
        $messages = array();


//        original code
//        $parameters = $params;
        $response = $this->request('address/validate', (array)$params);
        $parsed_response = json_decode($response->getBody()->getContents())->result;
//        $response_body = $response->getBody()->getContents();
//        $result = $parsed_response['result'][0];
//        $address = $parsed_response['result'][0]['address'];

        return $parsed_response;
//        return $parsed_response->result;
        // TODO: implement the below once we get the hoverfly response to match the spec
//        return $this->deserializeJsonToType($parsed_response, AddressValidateResult::class);
//        return $this->deserializeJsonToType(json_encode($parsed_response), AddressValidateResult::class);

//        $status_code = $response->getStatusCode();
    }
}
