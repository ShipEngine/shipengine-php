<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\Exception\ErrorException;

use ShipEngine\Model\Address;
use ShipEngine\Model\AddressQuery;
use ShipEngine\Model\AddressQueryResult;

final class AddressesService extends AbstractService
{

    /**
     *
     */
    private function parseNormalized($obj): ?Address
    {
        $matched = $obj[0]['matched_address'];
        if (is_null($matched)) {
            return null;
        }
        
        $lines = array($matched['address_line1'], $matched['address_line2'], $matched['address_line3']);
        $street = array_filter($lines);
        
        $city_locality = $matched['city_locality'];
        $state_province = $matched['state_province'];
        $postal_code = $matched['postal_code'];
        $country = $matched['country_code'];

        switch ($matched['address_residential_indicator']) {
            case 'yes':
                $residential = true;
                break;
            case 'no':
                $residential = false;
                break;
            default:
                $residential = null;
        }
        
        return new Address($street, $city_locality, $state_province, $postal_code, $country, $residential);
    }

    /**
     *
     */
    private function parseExceptions($obj): array
    {
        $messages = $obj[0]['messages'];
        if (count($messages) === 0) {
            return array();
        }

        $info = array();
        $warnings = array();
        $errors = array();
        
        foreach ($messages as $message) {
            $details = $message['message'];
            switch ($message['type']) {
                case 'info':
                    $info[] = new InfoException($details);
                    break;
                case 'warning':
                    $warnings[] = new WarningException($details);
                    break;
                case 'error':
                    $errors[] = new ErrorException($details);
                    break;
            }
        }

        return array_merge($info, $warnings, $errors);
    }

    /**
     *
     */
    public function query(AddressQuery $address_query): AddressQueryResult
    {
        $json = $this->jsonize($address_query, ['street', 'address_line1'], ['country', 'country_code']);
        $response = $this->request('POST', '/addresses/validate', $json);

        $body = json_decode((string) $response->getBody(), true);

        $normalized = $this->parseNormalized($body);
        $exceptions = $this->parseExceptions($body);

        return new AddressQueryResult($address_query, $normalized, $exceptions);
    }

    /**
     *
     */
    public function validate(AddressQuery $address_query): bool
    {
        $result = $this->query($address_query);
        if (!is_null($result->normalized) && empty($result->errors())) {
            return true;
        }

        return false;
    }

    /**
     *
     */
    public function normalize(AddressQuery $address_query): ?Address
    {
        $result = $this->query($address_query);

        if (!is_null($result->normalized) && empty($result->errors())) {
            return $result->normalized;
        }

        throw new ErrorException("no matching address found for address query");
    }
}
