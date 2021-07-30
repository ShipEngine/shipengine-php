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

/**
 * Class Assert
 * @package ShipEngine\Util
 */
final class Assert
{
    /**
     * Asserts that the API Key provided is a valid string and is provided.
     *
     * @param array $config
     */
    public function isApiKeyValid(array $config): void
    {
        if (isset($config['apiKey']) === false || $config['apiKey'] === '') {
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

    // /**
    //  * Asserts that the status code is 500, and if it is a `SystemException` is thrown.
    //  *
    //  * @param int $statusCode
    //  * @param array $parsedResponse
    //  */
    // public function isResponse500(int $statusCode, array $parsedResponse): void
    // {
    //     if ($statusCode === 500) {
    //         $error = $parsedResponse['error'];
    //         throw new SystemException(
    //             $error['message'],
    //             $parsedResponse['id'],
    //             $error['data']['source'],
    //             $error['data']['type'],
    //             $error['data']['code'],
    //             $error['data']['url'] ?? null
    //         );
    //     }
    // }

    // public function isResponse404(int $statusCode, $parsedResponse): void
    // {
    //     var_dump($parsedResponse);
    //     if (array_key_exists('error', $parsedResponse)) {
    //         $error = $parsedResponse['error'];
    //         $errorData = $parsedResponse['error']['data'];
    //         if ($statusCode === 404) {
    //             throw new SystemException(
    //                 $error['message'],
    //                 $parsedResponse['id'],
    //                 $errorData['source'],
    //                 $errorData['type'],
    //                 $errorData['code'],
    //                 null
    //             );
    //         }
    //     }
    // }

    // public function isResponse429(int $statusCode, array $response, ShipEngineConfig $config): void
    // {
    //     if (array_key_exists('error', $response)) {
    //         $error = $response['error'];
    //         $retryAfter = isset($error['data']['retryAfter']) ? $error['data']['retryAfter'] : null;

    //         if ($retryAfter > $config->timeout->s) {
    //             throw new TimeoutException(
    //                 $config->timeout->s,
    //                 ErrorSource::SHIPENGINE,
    //                 $response['id']
    //             );
    //         }

    //         if ($statusCode === 429) {
    //             throw new RateLimitExceededException(
    //                 new \DateInterval("PT{$retryAfter}S"),
    //                 ErrorSource::SHIPENGINE,
    //                 $response['id']
    //             );
    //         }
    //     }
    // }
}
