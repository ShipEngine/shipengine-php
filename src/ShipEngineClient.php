<?php declare(strict_types=1);

namespace ShipEngine;

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

/**
 * A wrapped `REST` HTTP client to send HTTP requests from the SDK.
 *
 * @package ShipEngine
 */
final class ShipEngineClient
{

    /**
     * Implement a GET request and return output
     *
     * @param string $path
     * @param ShipEngineConfig $config
     *
     * @return array
     */
    public function get($path, ShipEngineConfig $config)
    {
        return $this->sendRequestWithRetries('GET', $path, null, $config);
    }

    /**
     * Implement a POST request and return output
     *
     * @param string $path
     * @param ShipEngineConfig $config
     * @param array $params
     *
     * @return array
     */
    public function post($path, ShipEngineConfig $config, array $params = null)
    {
        return $this->sendRequestWithRetries('POST', $path, $params, $config);
    }

    /**
     * Implement a PUT request and return output
     *
     * @param string $path
     * @param ShipEngineConfig $config
     * @param array $params
     *
     * @return array
     */
    public function put($path, ShipEngineConfig $config, array $params = null)
    {
        return $this->sendRequestWithRetries('PUT', $path, $params, $config);
    }

    /**
     * Implement a DELETE request and return output
     *
     * @param string $path
     * @param ShipEngineConfig $config
     *
     * @return array
     */
    public function delete($path, ShipEngineConfig $config)
    {
        return $this->sendRequestWithRetries('DELETE', $path, null, $config);
    }

    /**
     * Send a `REST` request via *ShipEngineClient*.
     *
     * @param string $method
     * @param string $path
     * @param array|null $params
     * @param ShipEngineConfig $config
     * @return array
     * @throws GuzzleException
     */
    private function sendRequestWithRetries(
        string $method,
        string $path,
        ?array $params,
        ShipEngineConfig $config
    ): array
    {
        $apiResponse = null;
        for ($retry = 0; $retry <= $config->retries; $retry++) {
            try {
                $apiResponse = $this->sendRequest($method, $path, $params, $retry, $config);
                break;
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
     * @param string $method
     * @param string $path
     * @param array|null $params
     * @param int $retry
     * @param ShipEngineConfig $config
     * @return array
     * @throws GuzzleException
     */
    private function sendRequest(
        string $method,
        string $path,
        ?array $params,
        int $retry,
        ShipEngineConfig $config
    ): array {
        $requestHeaders = array(
            'api-key' => $config->apiKey,
            'User-Agent' => $this->deriveUserAgent(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        );

        $client = new Client(
            [
                'base_uri' => $config->baseUrl,
                'timeout' => $config->timeout,
                'max_retry_attempts' => $config->retries
            ]
        );

        $jsonData = json_encode($params, JSON_UNESCAPED_SLASHES);

        $request = new Request($method, $path, $requestHeaders, $jsonData);

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
                'ShipEngine',
                'System',
                'Unspecified'
            );
        }

        $responseBody = (string)$response->getBody();

        $parsedResponse = json_decode($responseBody, true);
        $statusCode = $response->getStatusCode();

        // $assert->isResponse404($statusCode, $parsedResponse);
        // $assert->isResponse429($statusCode, $parsedResponse, $config);
        // $assert->isResponse500($statusCode, $parsedResponse);

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
        if (!isset($response['errors']) || (count($response['errors']) == 0)) {
            return $response;
        }

        $error = $response['errors'][0];

        throw new ShipEngineException(
            $error['message'],
            $response['request_id'],
            $error['error_source'],
            $error['error_type'],
            $error['error_code']
        );
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
