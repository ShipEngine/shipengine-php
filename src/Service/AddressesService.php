<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Rakit\Validation\Validator;

use ShipEngine\Message\Error;
use ShipEngine\Message\Info;
use ShipEngine\Message\Warning;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\Query;
use ShipEngine\Model\Address\QueryResult;

/**
 * Service to query, normalize, and validate addresses.
 */
final class AddressesService extends AbstractService
{

    /**
     * Parse the `matched_address` of the address query result into a normalized \ShipEngine\Model\Address\Address.
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
     * Parse the `messages` of the address query result into \ShipEngine\Message\Message types.
     */
    private function parseMessages($obj): array
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
                    $info[] = new Info($details);
                    break;
                case 'warning':
                    $warnings[] = new Warning($details);
                    break;
                case 'error':
                    $errors[] = new Error($details);
                    break;
            }
        }

        return array_merge($info, $warnings, $errors);
    }

    /**
     * Query an \ShipEngine\Model\Address\Query to receive the full \ShipEngine\Model\Address\QueryResult.
     */
    public function query(Query $query): QueryResult
    {
        $json = $this->jsonize($query, ['street', 'address_line1'], ['country', 'country_code']);
        $body = $this->request('POST', '/addresses/validate', $json);

        $normalized = $this->parseNormalized($body);
        $messages = $this->parseMessages($body);

        return new QueryResult($query, $normalized, $messages);
    }

    /**
     * Validate that an \ShipEngine\Model\Address\Query matches a known \ShipEngine\Model\Address\Address.
     */
    public function validate(Query $query): bool
    {
        $result = $this->query($query);
        if (!is_null($result->normalized) && empty($result->errors())) {
            return true;
        }

        return false;
    }
    
    /**
     * Normalize an \ShipEngine\Model\Address\Query into a known \ShipEngine\Model\Address\Address.
     *
     * @throws \ShipEngine\Message\Error if the Query cannot be normalized.
     */
    public function normalize(Query $query): ?Address
    {
        $result = $this->query($query);
        if (!is_null($result->normalized) && empty($result->errors())) {
            return $result->normalized;
        }

        throw new Error("no matching address found for address query");
    }
}
