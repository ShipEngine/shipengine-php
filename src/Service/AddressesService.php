<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\Exception\ErrorException;
use ShipEngine\Exception\InfoException;
use ShipEngine\Exception\ShipEngineException;
use ShipEngine\Exception\WarningException;

use ShipEngine\Model\Address;
use ShipEngine\Model\AddressQuery;
use ShipEngine\Model\AddressQueryResult;

/**
 * Service to query, normalize, and validate addresses.
 */
final class AddressesService extends AbstractService
{

    /**
     * Parse the `matched_address` of the address query result into a normalized \ShipEngine\Model\Address.
     */
    private function parseNormalized($obj): ?Address
    {
        $matched = $obj[0]['matched_address'];
        if (is_null($matched)) {
            return null;
        }
        
        $lines = array($matched['address_line1'], $matched['address_line2'], $matched['address_line3']);
        $street = array_filter($lines);
        if (empty($street)) {
            $street = array('');
        }
        
        $city_locality = $matched['city_locality'];
        if (is_null($city_locality)) {
            $city_locality = '';
        }
        
        $state_province = $matched['state_province'];
        if (is_null($state_province)) {
            $state_province = '';
        }
        
        $postal_code = $matched['postal_code'];
        if (is_null($postal_code)) {
            $postal_code = '';
        }
        
        $country = $matched['country_code'];
        if (is_null($country)) {
            $country = '';
        }

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
     * Parse the `messages` of the address query result into \ShipEngine\Exception\ShipEngineException types.
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
                default:
                    $errors[] = new ShipEngineException($details);
            }
        }

        return array_merge($info, $warnings, $errors);
    }

    /**
     * Query an \ShipEngine\Model\AddressQuery to receive the full \ShipEngine\Model\AddressQueryResult.
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
     * Validate that an \ShipEngine\Model\AddressQuery matches a known \ShipEngine\Model\Address.
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
     * Normalize an \ShipEngine\Model\AddressQuery into a known \ShipEngine\Model\Address.
     *
     * @throws \ShipEngine\Exception\ErrorException if the AddressQuery cannot be normalized.
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
