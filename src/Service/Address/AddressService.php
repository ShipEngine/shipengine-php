<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Model\Address\Address;
use ShipEngine\Service\AbstractService;

final class AddressService extends AbstractService
{
    /**
     * Validate a single address via the `address/validate` remote procedure.
     *
     * @param array $params
     * @return Address
     */
    public function validate(array $params): Address
    {
        $response = $this->request('address/validate', $params);
        $parsed_response =  json_decode($response->getBody()->getContents(), true);

        return new Address(
            $parsed_response['result'][0]['address']['street'],
            $parsed_response['result'][0]['address']['city_locality'],
            $parsed_response['result'][0]['address']['state_province'],
            $parsed_response['result'][0]['address']['postal_code'],
            $parsed_response['result'][0]['address']['country_code'],
            $parsed_response['result'][0]['address']['residential']
        );

//        return $parsed_response['result'][0]['address']['city_locality'];
    }
}
