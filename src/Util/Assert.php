<?php declare(strict_types=1);

namespace ShipEngine\Util;

use ShipEngine\Message\BusinessRuleException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorSource;
use ShipEngine\Util\Constants\ErrorType;

/**
 * Class Assert
 * @package ShipEngine\Util
 */
final class Assert
{
    /**
     * Asserts that street array is not empty.
     *
     * @param array $street
     */
    public function isStreetSet(array $street)
    {
        if (empty($street)) {
            throw new ValidationException(
                'Invalid address. At least one address line is required.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        }
    }

    /**
     * Checks if the street array has too many address lines, and
     * will return false if the array **does not** have too many
     * address lines.
     *
     * @param array $street
     * @throws ValidationException
     */
    public function tooManyAddressLines(array $street)
    {
        if (count($street) > 3) {
            throw new ValidationException(
                'Invalid address. No more than 3 street lines are allowed.',
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        }
    }

    /**
     * Asserts that city in not an empty string and is valid characters.
     *
     * @param string $city_locality
     * @throws ValidationException
     */
    public function isCityValid(string $city_locality)
    {
        if (preg_match('/^[a-zA-Z0-9\s\W]*$/', $city_locality) === false || $city_locality === '') {
            throw new ValidationException(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        }
    }

    /**
     * Asserts that state is 2 capitalized letters and that it is not an empty string.
     *
     * @param string $state_province
     * @throws ValidationException
     */
    public function isStateValid(string $state_province)
    {
        if (preg_match('/^[A-Z\W]{2}$/', $state_province) === false || $state_province === '') {
            throw new ValidationException(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        }
    }

    /**
     * Asserts that the postal code contains to allowed characters and is not an empty string.
     *
     * @param string $postal_code
     * @throws ValidationException
     */
    public function isPostalCodeValid(string $postal_code)
    {
        if (preg_match('/^[a-zA-Z0-9\s-]*$/', $postal_code) === false || $postal_code == '') {
            throw new ValidationException(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        }
    }

    /**
     * Check if the country code is 2 capitalized letter and is not an empty string.
     *
     * @param string $country_code
     * @throws ValidationException
     */
    public function isCountryCodeValid(string $country_code)
    {
        if ($country_code == '') {
            throw new ValidationException(
                "Invalid address. The country must be specified.",
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        } elseif (!preg_match('/^[A-Z]{2}$/', $country_code)) {
            throw new ValidationException(
                "Invalid address. {$country_code} is not a valid country code.",
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        }
    }

    /**
     * Asserts that the API Key provided is a valid string and is provided.
     *
     * @param array $config
     */
    public function isApiKeyValid(array $config): void
    {
        if (isset($config['api_key']) === false || $config['api_key'] === '') {
            throw new ValidationException(
                'A ShipEngine API key must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        }
    }

    /**
     * Asserts that the timeout value is valid.
     *
     * @param \DateInterval $timeout
     */
    public function isTimeoutValid(\DateInterval $timeout): void
    {
        if ($timeout->invert === 1 || $timeout->s === 0) {
            throw new ValidationException(
                'Timeout must be greater than zero.',
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        }
    }

    /**
     * Asserts that the status code is 500, and if it is a `SystemException` is thrown.
     *
     * @param array $parsed_response
     * @param int $status_code
     */
    public function doesResponseHave500Error(array $parsed_response, int $status_code)
    {
        if ($status_code === 500) {
            $error = $parsed_response['error'];
            throw new SystemException(
                $error['message'],
                $parsed_response['id'],
                $error['data']['source'],
                $error['data']['type'],
                $error['data']['code'],
                $error['data']['url'] ?? null
            );
        }
    }

    /**
     * Assertions to check if the returned normalized address has any errors. If errors
     * are present an exception is thrown.
     *
     * @param AddressValidateResult $validation_result
     * @return mixed
     */
    public function doesNormalizedAddressHaveErrors(AddressValidateResult $validation_result)
    {
        if (count($validation_result->errors) > 1) {
            throw new BusinessRuleException(
                "Invalid address.\n" . implode("\n", $validation_result->errors),
                $validation_result->request_id,
                ErrorSource::SHIPENGINE,
                ErrorType::BUSINESS_RULES,
                ErrorCode::INVALID_ADDRESS,
                null
            );
        } elseif (count($validation_result->errors) === 1) {
            throw new BusinessRuleException(
                "Invalid address. " . $validation_result->errors[0],
                $validation_result->request_id,
                ErrorSource::SHIPENGINE,
                ErrorType::BUSINESS_RULES,
                ErrorCode::INVALID_ADDRESS,
                null
            );
        } elseif (!$validation_result->valid) {
            throw new BusinessRuleException(
                'Invalid address - The address provided could not be normalized.',
                $validation_result->request_id,
                ErrorSource::SHIPENGINE,
                ErrorType::BUSINESS_RULES,
                ErrorCode::INVALID_ADDRESS,
                null
            );
        }
    }
}
