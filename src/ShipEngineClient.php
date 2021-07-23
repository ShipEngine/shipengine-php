<?php declare(strict_types=1);

namespace ShipEngine;

use cbschuld\UuidBase58;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\AccountStatusException;
use ShipEngine\Message\BusinessRuleException;
use ShipEngine\Message\Events\EventMessage;
use ShipEngine\Message\Events\EventOptions;
use ShipEngine\Message\Events\RequestSentEvent;
use ShipEngine\Message\Events\ResponseReceivedEvent;
use ShipEngine\Message\Events\ShipEngineEvent;
use ShipEngine\Message\RateLimitExceededException;
use ShipEngine\Message\SecurityException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Util\Assert;
use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorSource;
use ShipEngine\Util\Constants\ErrorType;

/**
 * A wrapped `JSON-RPC 2.0` HTTP client to send HTTP requests from the SDK.
 *
 * @package ShipEngine
 */
final class ShipEngineClient
{
//    /**
//     * Wrap request per `JSON-RPC 2.0` spec.
//     *
//     * @param string $method
//     * @param array|null $params
//     * @return array
//     */
//    private function wrapRequest(string $method, ?array $params): array
//    {
//        if ($params === null) {
//            return array_filter([
//                'id' => 'req_' . UuidBase58::id(),
//                'jsonrpc' => '2.0',
//                'method' => $method
//            ]);
//        } else {
//            return array_filter([
//                'id' => 'req_' . UuidBase58::id(),
//                'jsonrpc' => '2.0',
//                'method' => $method,
//                'params' => $params
//            ]);
//        }
//    }

    public function restRequest(string $http_method, string $endpoint, $body, ShipEngineConfig $config)
    {
        return $this->retryRequestLoop($http_method, $endpoint, $body, $config);
    }

    public function retryRequestLoop(string $http_method, string $endpoint, $body, ShipEngineConfig $config)
    {
        $api_response = null;
        for ($retry = 0; $retry <= $config->retries; $retry++) {
            try {
                $api_response = $this->sendRESTRequest($http_method, $endpoint, $body, $retry, $config);
            } catch (\RuntimeException $err) {
                if (($retry < $config->retries) &&
                    $err instanceof RateLimitExceededException &&
                    ($err->retryAfter->s < $config->timeout->s)
                ) {
                    // The request was blocked due to exceeding the rate limit.
                    // So wait the specified amount of time and then retry.
                    sleep($err->retryAfter->s);
                } else {
                    throw $err;
                }
            }
            return $api_response;
        }
    }

    public function sendRESTRequest(
        string $http_method,
        string $endpoint,
        $body,
        int $retry,
        ShipEngineConfig $config
    ) {
        $assert = new Assert();
        $baseUri = !getenv('CLIENT_BASE_URI') ? $config->base_url : getenv('CLIENT_BASE_URI');
        $requestHeaders = array(
            'Api-Key' => $config->api_key,
            'User-Agent' => $this->deriveUserAgent(ShipEngine::VERSION),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        );

        $internal_client = new Client([
            'base_uri' => $baseUri,
            'headers' => $requestHeaders,
            'http_errors' => false,
            'timeout' => $config->timeout,
            'max_retry_attempts' => $config->retries,
        ]);

        $request_body = json_encode($body, JSON_THROW_ON_ERROR);

        $request = new Request($http_method, $endpoint, $requestHeaders, $request_body);

        try {
            $response = $internal_client->send(
                $request,
                ['timeout' => $config->timeout->s, 'http_errors' => false]
            );
        } catch (ClientException $err) {
            throw new ShipEngineException(
                "An unknown error occurred while calling the ShipEngine $endpoint API:\n" .
                $err->getMessage(),
                null,
                ErrorSource::SHIPENGINE,
                ErrorType::SYSTEM,
                ErrorCode::UNSPECIFIED
            );
        }

        $responseBody = (string)$response->getBody();
        $parsedResponse = json_decode($responseBody, true);
        $statusCode = $response->getStatusCode();

//        $assert->isResponse404($statusCode, $parsedResponse);
//        $assert->isResponse429($statusCode, $parsedResponse, $config);
//        $assert->isResponse500($statusCode, $parsedResponse);

//        return $response;
        return $this->handleResponse($parsedResponse);
    }


    /**
     * Handles the response from ShipEngine API.
     *
     * @param array $response
     * @return array
     */
    private function handleResponse(array $response): array
    {
        if (!isset($response['errors'])) {
            return $response;
        }

        $error = $response['errors'][0];

        switch ($error['error_type']) {
            case ErrorType::ACCOUNT_STATUS:
                throw new AccountStatusException(
                    $error['message'],
                    $response['request_id'],
                    $error['error_source'],
                    $error['error_type'],
                    $error['error_code']
                );
            case ErrorType::SECURITY:
                throw new SecurityException(
                    $error['message'],
                    $response['request_id'],
                    $error['error_source'],
                    $error['error_type'],
                    $error['error_code']
                );
            case ErrorType::VALIDATION:
                throw new ValidationException(
                    $error['message'],
                    $response['request_id'],
                    $error['error_source'],
                    $error['error_type'],
                    $error['error_code']
                );
            case ErrorType::BUSINESS_RULES:
                throw new BusinessRuleException(
                    $error['message'],
                    $response['request_id'],
                    $error['error_source'],
                    $error['error_type'],
                    $error['error_code']
                );
            case ErrorType::SYSTEM:
                throw new SystemException(
                    $error['message'],
                    $response['request_id'],
                    $error['error_source'],
                    $error['error_type'],
                    $error['error_code']
                );
            default:
                throw new ShipEngineException(
                    $error['message'],
                    $response['request_id'],
                    $error['error_source'],
                    $error['error_type'],
                    $error['error_code']
                );
        }
    }

    /**
     * Derive a User-Agent header from the environment. This is the user-agent that will be set on every request
     * via the ShipEngine Client.
     *
     * @returns string
     */
    private function deriveUserAgent(string $version): string
    {
        $sdk_version = 'shipengine-php/' . $version;

        $os = explode(' ', php_uname());
        $os_kernel = $os[0] . '/' . $os[2];

        $php_version = 'PHP/' . phpversion();

        return $sdk_version . ' ' . $os_kernel . ' ' . $php_version;
    }
}
