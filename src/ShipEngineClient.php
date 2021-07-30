<?php

declare(strict_types=1);

namespace ShipEngine;

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
 * An HTTP client.
 *
 * @package ShipEngine
 */
final class ShipEngineClient
{
    /**
     * Make an HTTP GET request.
     *
     * @param string $path The path to send the request to.
     * @param ShipEngineConfig $config A ShipEngineConfig object.
     * @return object
     * @throws ClientExceptionInterface
     */
    public function get(string $path, ShipEngineConfig $config): object
    {
        return $this->sendHTTPRequest($path, $config);
    }

    /**
     * Make an HTTP POST request.
     *
     * @param string $path The path to send the request to.
     * @param ShipEngineConfig $config A ShipEngineConfig object.
     * @param object|null $body An array of params to be sent in the JSON-RPC request.
     * @return object
     * @throws ClientExceptionInterface
     */
    public function post(string $path, ShipEngineConfig $config, array $params = null): object
    {
        return $this->sendHTTPRequest($method, $params, $config);
    }
    /**
     * Make an HTTP PUT request.
     *
     * @param string $path The path to send the request to.
     * @param ShipEngineConfig $config A ShipEngineConfig object.
     * @param array|null $params An array of params to be sent in the JSON-RPC request.
     * @return array
     * @throws ClientExceptionInterface
     */
    public function put(string $method, ShipEngineConfig $config, array $params = null): array
    {
        return $this->sendHTTPRequest($path, $params, $config);
    }

    /**
     * Make an HTTP DELETE request.
     *
     * @param string $path The path to send the request to.
     * @param ShipEngineConfig $config A ShipEngineConfig object.
     * @param array|null $params An array of params to be sent in the JSON-RPC request.
     * @return array
     * @throws ClientExceptionInterface
     */
    public function delete(string $path, ShipEngineConfig $config, array $params = null): array
    {
        return $this->sendHTTPRequest($method, $params, $config);
    }

    /**
     * Make an HTTP request to the ShipEngine API. If the response
     * is successful, the result is returned. Otherwise, the request will be retried.
     *
     * @param string $method
     * @param object|null $object
     * @param ShipEngineConfig $config
     * @return object
     * @throws GuzzleException
     */
    private function sendHTTPRequest(string $method, ?object $object, ShipEngineConfig $config): object
    {
        $apiResponse = null;
        for ($retry = 0; $retry <= $config->retries; $retry++) {
            try {
                $apiResponse = $this->sendRequest($method, $object, $retry, $config);
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
        }
        return $apiResponse;
    }

    /**
     * Make an HTTP request to the ShipEngine API. If the response
     * is successful, the result is returned. Otherwise, an error is thrown.
     *
     * @param string $method
     * @param object|null $body
     * @param int $retry
     * @param ShipEngineConfig $config
     * @return array
     * @throws GuzzleException
     */
    private function sendRequest(
        string $method,
        ?object $body,
        int $retry,
        ShipEngineConfig $config
    ): array {
        $assert = new Assert();
        $baseUri = !getenv('CLIENT_BASE_URI') ? $config->baseUrl : getenv('CLIENT_BASE_URI');
        $requestHeaders = array(
            'Api-Key' => $config->apiKey,
            'User-Agent' => $this->deriveUserAgent(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        );

        $client = new Client(
            [
                'baseUri' => $baseUri,
                'headers' => $requestHeaders,
                'max_retry_attempts' => $config->retries
            ]
        );

        $jsonData = json_encode($body, JSON_UNESCAPED_SLASHES);

        $retry === 0 ?
            $requestEventMessage = EventMessage::newEventMessage($method, $baseUri, 'base_message') :
            $requestEventMessage = EventMessage::newEventMessage($method, $baseUri, 'retry_message');

        $requestEventData = new EventOptions([
            'message' => $requestEventMessage,
            'id' => $body['id'],
            'baseUri' => $baseUri,
            'requestHeaders' => $requestHeaders,
            'body' => $body,
            'retry' => $retry,
            'timeout' => $config->timeout
        ]);

        $requestSentEvent = ShipEngineEvent::emitEvent(
            RequestSentEvent::REQUEST_SENT,
            $requestEventData,
            $config
        );

        $request = new Request('POST', $baseUri, $requestHeaders, $jsonData);

        try {
            $response = $client->send(
                $request,
                ['timeout' => $config->timeout->s, 'http_errors' => false]
            );
        } catch (ClientException $err) {
            throw new ShipEngineException(
                "An unknown error occurred while calling the ShipEngine $method API:\n" .
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

        $responseEventData = new EventOptions([
            'message' => "Received an HTTP $statusCode response from the ShipEngine $method API",
            'id' => $parsedResponse['id'],
            'baseUri' => $baseUri,
            'statusCode' => $statusCode,
            'responseHeaders' => $response->getHeaders(),
            'body' => $parsedResponse,
            'retry' => $retry,
            'elapsed' => (new \DateTime())->diff($requestSentEvent->timestamp)
        ]);

        ShipEngineEvent::emitEvent(
            ResponseReceivedEvent::RESPONSE_RECEIVED,
            $responseEventData,
            $config
        );

        $assert->isResponse404($statusCode, $parsedResponse);
        $assert->isResponse429($statusCode, $parsedResponse, $config);
        $assert->isResponse500($statusCode, $parsedResponse);

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
        if (isset($response['result']) === true) {
            return $response;
        }

        $error = $response['error'];

        switch ($error['data']['type']) {
            case ErrorType::ACCOUNT_STATUS:
                throw new AccountStatusException(
                    $error['message'],
                    $response['id'],
                    $error['data']['source'],
                    $error['data']['type'],
                    $error['data']['code']
                );
            case ErrorType::SECURITY:
                throw new SecurityException(
                    $error['message'],
                    $response['id'],
                    $error['data']['source'],
                    $error['data']['type'],
                    $error['data']['code']
                );
            case ErrorType::VALIDATION:
                throw new ValidationException(
                    $error['message'],
                    $response['id'],
                    $error['data']['source'],
                    $error['data']['type'],
                    $error['data']['code']
                );
            case ErrorType::BUSINESS_RULES:
                throw new BusinessRuleException(
                    $error['message'],
                    $response['id'],
                    $error['data']['source'],
                    $error['data']['type'],
                    $error['data']['code']
                );
            case ErrorType::SYSTEM:
                throw new SystemException(
                    $error['message'],
                    $response['id'],
                    $error['data']['source'],
                    $error['data']['type'],
                    $error['data']['code']
                );
            default:
                throw new ShipEngineException(
                    $error['message'],
                    $response['id'],
                    $error['data']['source'],
                    $error['data']['type'],
                    $error['data']['code']
                );
        }
    }

    /**
     * Derive a User-Agent header from the environment. This is the user-agent that will be set on every request
     * via the ShipEngine Client.
     *
     * @returns string
     */
    private function deriveUserAgent(): string
    {
        $sdk_version = 'shipengine-php/' . ShipEngine::VERSION;

        $os = explode(' ', php_uname());
        $os_kernel = $os[0] . '/' . $os[2];

        $php_version = 'PHP/' . phpversion();

        return $sdk_version . ' ' . $os_kernel . ' ' . $php_version;
    }
}
