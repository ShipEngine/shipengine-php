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
use ShipEngine\Message\Events\RequestSentEvent;
use ShipEngine\Message\Events\ResponseReceivedEvent;
use ShipEngine\Message\RateLimitExceededException;
use ShipEngine\Message\SecurityException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Util\Assert;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorSource;
use ShipEngine\Util\Constants\ErrorType;
use ShipEngine\Util\VersionInfo;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * A wrapped `JSON-RPC 2.0` HTTP client to send HTTP requests from the SDK.
 *
 * @package ShipEngine
 */
final class ShipEngineClient
{
    /**
     * Wrap request per `JSON-RPC 2.0` spec.
     *
     * @param string $method
     * @param array $params
     * @return array
     */
    private function wrapRequest(string $method, array $params): array
    {
        return array_filter([
            'id' => 'req_' . UuidBase58::id(),
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params
        ]);
    }

    /**
     * Create and send a `JSON-RPC 2.0` request over HTTP messages.
     *
     * @param string $method The RPC method to be used in the request.
     * @param ShipEngineConfig $config A ShipEngineConfig object.
     * @param array $params An array of params to be sent in the JSON-RPC request.
     * @return array|mixed
     * @throws ClientExceptionInterface
     */
    public function request(string $method, ShipEngineConfig $config, array $params = array())
    {
        return $this->sendRPCRequest($method, $params, $config);
    }

    /**
     * Send a `JSON-RPC 2.0` request via *ShipEngineClient*.
     *
     * @param string $method
     * @param array $params
     * @param ShipEngineConfig $config
     * @return mixed
     * @throws ClientExceptionInterface
     */
    private function sendRPCRequest(string $method, array $params, ShipEngineConfig $config)
    {
        for ($retry = 0; $retry <= $config->retries; $retry++) {
            try {
                return $this->sendRequest($method, $params, $retry, $config);
            } catch (\RuntimeException $err) {
                if (($retry < $config->retries) &&
                    ($err instanceof RateLimitExceededException) &&
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
    }

    /**
     * Send a `JSON-RPC 2.0` request via HTTP Messages to ShipEngine API. If the response
     * is successful, the result is returned. Otherwise, an error is thrown.
     *
     * @param string $method
     * @param array $params
     * @param int $retry
     * @param ShipEngineConfig $config
     * @return mixed
     * @throws GuzzleException
     */
    private function sendRequest(
        string $method,
        array $params,
        int $retry,
        ShipEngineConfig $config
    ) {
        $assert = new Assert();
        $base_uri = !getenv('CLIENT_BASE_URI') ? $config->base_url : getenv('CLIENT_BASE_URI');
        $dispatcher = new EventDispatcher();
        $shipengine_event_listener = $config->event_listener;
        $request_headers = array(
            'Api-Key' => $config->api_key,
            'User-Agent' => $this->deriveUserAgent(),
            'Content-Type' => 'application/json'
        );

        $body = $this->wrapRequest($method, $params);

        // Config for the Guzzle Client
        $guzzle_config = array(
            'base_uri' => $base_uri,
            'headers' => $request_headers
        );

        $client = new Client($guzzle_config);

        $jsonData = json_encode($body, JSON_UNESCAPED_SLASHES);

        $request_sent_event = new RequestSentEvent(
            "Calling the ShipEngine $method API at $base_uri",
            $body['id'],
            $base_uri,
            $request_headers,
            $body,
            $retry,
            $config->timeout
        );

        $dispatcher->addListener(
            $request_sent_event::REQUEST_SENT,
            [$shipengine_event_listener, 'onRequestSent']
        );
        $dispatcher->dispatch($request_sent_event, $request_sent_event::REQUEST_SENT);

        $request = new Request('POST', $base_uri, $request_headers, $jsonData);

        try {
            $response = $client->send($request, [
                'timeout' => $config->timeout->s,
                'http_errors' => false
            ]);
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

        $response_body = (string)$response->getBody();
        $parsed_response = json_decode($response_body, true);
        $status_code = $response->getStatusCode();

        $response_received_event = new ResponseReceivedEvent(
            "Response Received",
            $parsed_response['id'],
            $base_uri,
            $status_code,
            $response->getHeaders(),
            $parsed_response,
            $retry,
            (new \DateTime())->diff($request_sent_event->timestamp)
        );
        $dispatcher->addListener(
            $response_received_event::RESPONSE_RECEIVED,
            [$shipengine_event_listener, 'onResponseReceived']
        );
        $dispatcher->dispatch($response_received_event, $response_received_event::RESPONSE_RECEIVED);

        $assert->doesResponseHave500Error($parsed_response, $status_code);

        return $this->handleResponse($parsed_response);
    }


    /**
     * @param array $response
     * @return mixed
     */
    private function handleResponse(array $response)
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
            case ErrorCode::RATE_LIMIT_EXCEEDED:
                $retryAfter = $error['data']['retry_after'] * 1000;
                throw new RateLimitExceededException(
                    new \DateInterval("PT{$retryAfter}S"),
                    ErrorSource::SHIPENGINE,
                    $response['id']
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
        $sdk_version = 'shipengine-php/' . VersionInfo::string();

        $os = explode(' ', php_uname());
        $os_kernel = $os[0] . '/' . $os[2];

        $php_version = 'PHP/' . phpversion();

        return $sdk_version . ' ' . $os_kernel . ' ' . $php_version;
    }
}
