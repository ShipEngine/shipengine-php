<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Rakit\Validation\Validator;

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
        // Check that we get a matched address before trying to validate it.
        if (empty($obj)) {
            return null;
        }
        if (!array_key_exists('matched_address', $obj[0])) {
            return null;
        }
        $matched = $obj[0]['matched_address'];
        if (is_null($matched)) {
            return null;
        }
        
        $validator = new Validator();
        
        $guard = array(
            'address_line1' => 'default:|present',
            'address_line2' => 'default:|present',
            'address_line3' => 'default:|present',
            'city_locality' => 'default:|present',
            'state_province' => 'default:|present',
            'postal_code' => 'default:|present',
            'country_code' => 'default:|present',
            'address_residential_indicator' => 'present'
        );
        
        $validation = $validator->validate($matched, $guard);

        if ($validation->fails()) {
            return null;
        }
        
        $validated = $validation->getValidData();
        
        $lines = array($validated['address_line1'], $validated['address_line2'], $validated['address_line3']);
        $street = array_filter($lines);
        if (empty($street)) {
            $street = array('');
        }

        $residential = null;
        switch ($validated['address_residential_indicator']) {
            case 'yes':
                $residential = true;
                break;
            case 'no':
                $residential = false;
                break;
        }
        
        return new Address(
            $street,
            $validated['city_locality'],
            $validated['state_province'],
            $validated['postal_code'],
            $validated['country_code'],
            $residential
        );
    }

    /**
     * Parse the `messages` of the address query result into \ShipEngine\Exception\ShipEngineException types.
     */
    private function parseExceptions($obj): array
    {
        // Check that we have messages before validating and casting them.
        if (empty($obj)) {
            return array();
        }
        if (!array_key_exists('messages', $obj[0])) {
            return array();
        }
        $messages = $obj[0]['messages'];
        if (count($messages) === 0) {
            return array();
        }

        $info = array();
        $warnings = array();
        $errors = array();

        $validator = new Validator();

        $guard = array(
            'type' => 'required|alpha',
            'message' => 'required'
        );

        foreach ($messages as $message) {
            $validation = $validator->validate($message, $guard);
            if ($validation->fails()) {
                continue;
            }

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
