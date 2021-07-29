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

    /**
     * Implement a GET request and return output
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function get($url, $httpHeaders = array())
    {
        //Initialize the Curl resource
        $ch = self::init($url, $httpHeaders);

        return self::processRequest($ch);
    }

    /**
     * Implement a POST request and return output
     *
     * @param string $url
     * @param array $data
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function post($url, $data, $httpHeaders = array())
    {
        $ch = self::init($url, $httpHeaders);
        //Set the request type
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        return self::processRequest($ch);
    }

    /**
     * Implement a PUT request and return output
     *
     * @param string $url
     * @param array $data
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function put($url, $data, $httpHeaders = array())
    {
        $ch = self::init($url, $httpHeaders);
        //set the request type
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        return self::processRequest($ch);
    }

    /**
     * Implement a DELETE request and return output
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function delete($url, ShipEngineConfig $config, array $params = null)
    {
        $ch = self::init($url, $httpHeaders);
        //set the request type
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        return self::sendRESTRequest('DELETE', $params, $config);
    }
    

    /**
     * Create a `REST` request over HTTP messages.
     *
     * @param string $method The REST method to be used in the request.
     * @param string $url The REST url to be used in the request.
     * @param array|null $params An array of params to be sent in the REST request.
     * @param ShipEngineConfig $config A ShipEngineConfig object.
     * @return array
     * @throws ClientExceptionInterface
     */
    
    private function createRequest(string $method, string $url, ?array $params, ShipEngineConfig $config): Request
    {
        $assert = new Assert();
        $baseUri = !getenv('CLIENT_BASE_URI') ? $config->baseUrl : getenv('CLIENT_BASE_URI');
        $requestHeaders = array(
            'Api-Key' => $config->apiKey,
            'User-Agent' => $this->deriveUserAgent(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        );

        $body = $this->wrapRequest($method, $params);

        $jsonData = json_encode($body, JSON_UNESCAPED_SLASHES);
        
        return new Request($method, $baseUri, $requestHeaders, $jsonData);
    }

    /**
     * Send a `REST` request via *ShipEngineClient*.
     *
     * @param string $method
     * @param array|null $params
     * @param ShipEngineConfig $config
     * @return array
     * @throws GuzzleException
     */
    private function sendRESTRequest(string $method, string $url, ?array $params, ShipEngineConfig $config): array
    {
        $apiResponse = null;
        for ($retry = 0; $retry <= $config->retries; $retry++) {
            try {
                $request = createRequest($method, $url, $params, $config)
                $apiResponse = $this->sendRequest($request, $retry, $config);
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
     * Send a `REST` request via HTTP Messages to ShipEngine API. If the response
     * is successful, the result is returned. Otherwise, an error is thrown.
     *
     * @param Request $request
     * @param int $retry
     * @param ShipEngineConfig $config
     * @return array
     * @throws GuzzleException
     */
    private function sendRequest(
        Request $request,
        int $retry,
        ShipEngineConfig $config
    ): array {
        $client = new Client(
            [
                'baseUri' => $baseUri,
                'headers' => $requestHeaders,
                'max_retry_attempts' => $config->retries
            ]
        );

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
