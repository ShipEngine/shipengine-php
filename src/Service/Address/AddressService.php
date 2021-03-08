<?php declare(strict_types=1);

namespace ShipEngine\Service\Address;

use ShipEngine\Model\Address\Address;
use ShipEngine\Service\AbstractService;
use ShipEngine\ShipEngineError;

final class AddressService extends AbstractService
{
    /**
     * Validate a single address via the `address/validate` remote procedure.
     *
     * @param array $params
     * @return Address
     * @throws ShipEngineError if the provided address could not be validated.
     */
    public function validate(array $params): Address
    {
        $response = $this->request('address/validate', $params);
        $parsed_response = json_decode($response->getBody()->getContents(), true);

        if (empty($parsed_response['result'][0]['messages']['errors'])) {
            return new Address(
                $parsed_response['result'][0]['address']['street'],
                $parsed_response['result'][0]['address']['city_locality'],
                $parsed_response['result'][0]['address']['state_province'],
                $parsed_response['result'][0]['address']['postal_code'],
                $parsed_response['result'][0]['address']['country_code'],
                $parsed_response['result'][0]['address']['residential']
            );
        }

        $status_code = $response->getStatusCode();

//        $errors = array();
//        foreach ($parsed_response['result'][0]['messages']['errors'] as $error) {
//            $errors[] = $error;
//        }

        // TODO: check with Anthony on why this is not working properly
        throw new ShipEngineError('Failed to validate the provided address: ', $status_code);
    }
}
