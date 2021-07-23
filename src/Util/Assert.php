<?php declare(strict_types=1);

namespace ShipEngine\Util;

use DateInterval;
use ShipEngine\Message\RateLimitExceededException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\TimeoutException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\ShipEngineConfig;
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
    public function isStreetSet(array $street): void
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
    public function tooManyAddressLines(array $street): void
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
     * @param string $cityLocality
     * @throws ValidationException
     */
    public function isCityValid(string $cityLocality): void
    {
        if (preg_match('/^[a-zA-Z0-9\s\W]*$/', $cityLocality) === false || $cityLocality === '') {
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
     * @param string $stateProvince
     * @throws ValidationException
     */
    public function isStateValid(string $stateProvince): void
    {
        if (preg_match('/^[A-Z\W]{2}$/', $stateProvince) === false || $stateProvince === '') {
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
     * @param string $postalCode
     * @throws ValidationException
     */
    public function isPostalCodeValid(string $postalCode): void
    {
        if (preg_match('/^[a-zA-Z0-9\s-]*$/', $postalCode) === false || $postalCode === '') {
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
     * Check if the countryCode code is 2 capitalized letter and is not an empty string.
     *
     * @param string $countryCode
     * @throws ValidationException
     */
    public function isCountryCodeValid(string $countryCode): void
    {
        if ($countryCode === '') {
            throw new ValidationException(
                "Invalid address. The countryCode must be specified.",
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        } elseif (!preg_match('/^[A-Z]{2}$/', $countryCode)) {
            throw new ValidationException(
                "Invalid address. $countryCode is not a valid countryCode code.",
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
     * @param DateInterval $timeout
     */
    public function isTimeoutValid(DateInterval $timeout): void
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
     * @param int $statusCode
     * @param array $parsedResponse
     */
    public function isResponse500(int $statusCode, array $parsedResponse): void
    {
        if ($statusCode === 500) {
            $error = $parsedResponse['error'];
            throw new SystemException(
                $error['message'],
                $parsedResponse['id'],
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
     * @param AddressValidateResult $validationResult
     */
    public function doesNormalizedAddressHaveErrors(AddressValidateResult $validationResult): void
    {
        if (count($validationResult->errors) > 1) {
            $errorMessageArray = array();
            foreach ($validationResult->errors as $errorMessage) {
                $errorMessageArray[] = $errorMessage['message'];
            }

            throw new ShipEngineException(
                "Invalid address.\n" . implode("\n", $errorMessageArray) . "\n\n",
                $validationResult->requestId,
                ErrorSource::SHIPENGINE,
                'error',
                ErrorCode::INVALID_ADDRESS,
                null
            );
        } elseif (count($validationResult->errors) === 1) {
            throw new ShipEngineException(
                "Invalid address. " . $validationResult->errors[0]['message'],
                $validationResult->requestId,
                ErrorSource::SHIPENGINE,
                'error',
                $validationResult->errors[0]['code'],
                null
            );
        } elseif ($validationResult->isValid === false) {
            throw new ShipEngineException(
                'Invalid address - The address provided could not be normalized.',
                $validationResult->requestId,
                ErrorSource::SHIPENGINE,
                'error',
                ErrorCode::INVALID_ADDRESS,
                null
            );
        }
    }

    public function isPackageIdPrefixValid(string $packageId): void
    {
        $subString = substr($packageId, 0, 4);
        if ($subString !== 'pkg_') {
            throw new ValidationException(
                "[$subString] is not a valid package ID prefix.",
                null,
                ErrorSource::SHIPENGINE,
                ErrorType::VALIDATION,
                ErrorCode::INVALID_IDENTIFIER,
                null
            );
        }
    }

    public function isPackageIdValid(string $packageId): void
    {
        $this->isPackageIdPrefixValid($packageId);

        if (preg_match(
            '/^pkg_[123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz]+$/',
            $packageId
        ) === 0
        ) {
            throw new ValidationException(
                "[$packageId] is not a valid package ID.",
                null,
                ErrorSource::SHIPENGINE,
                ErrorType::VALIDATION,
                ErrorCode::INVALID_IDENTIFIER,
                null
            );
        }
    }

    public function isResponse404(int $statusCode, $parsedResponse): void
    {
        if (array_key_exists('error', $parsedResponse)) {
            $error = $parsedResponse['error'];
            $errorData = $parsedResponse['error']['data'];
            if ($statusCode === 404) {
                throw new SystemException(
                    $error['message'],
                    $parsedResponse['id'],
                    $errorData['source'],
                    $errorData['type'],
                    $errorData['code'],
                    null
                );
            }
        }
    }

    public function isResponse429(int $statusCode, array $response, ShipEngineConfig $config): void
    {
        if (array_key_exists('error', $response)) {
            $error = $response['error'];
            $retryAfter = isset($error['data']['retryAfter']) ? $error['data']['retryAfter'] : null;

            if ($retryAfter > $config->timeout->s) {
                throw new TimeoutException(
                    $config->timeout->s,
                    ErrorSource::SHIPENGINE,
                    $response['id']
                );
            }

            if ($statusCode === 429) {
                throw new RateLimitExceededException(
                    new \DateInterval("PT{$retryAfter}S"),
                    ErrorSource::SHIPENGINE,
                    $response['id']
                );
            }
        }
    }
}
