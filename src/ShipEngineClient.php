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
     * @param array|null $params
     * @return array
     */
    private function wrapRequest(string $method, ?array $params): array
    {
        if ($params === null) {
            return array_filter([
                'id' => 'req_' . UuidBase58::id(),
                'jsonrpc' => '2.0',
                'method' => $method
            ]);
        } else {
            return array_filter([
                'id' => 'req_' . UuidBase58::id(),
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => $params
            ]);
        }
    }

    /**
     * Create and send a `JSON-RPC 2.0` request over HTTP messages.
     *
     * @param string $method The RPC method to be used in the request.
     * @param ShipEngineConfig $config A ShipEngineConfig object.
     * @param array|null $params An array of params to be sent in the JSON-RPC request.
     * @return array|mixed
     * @throws ClientExceptionInterface
     */
    public function request(string $method, ShipEngineConfig $config, array $params = null)
    {
        return $this->sendRPCRequest($method, $params, $config);
    }

    /**
     * Send a `JSON-RPC 2.0` request via *ShipEngineClient*.
     *
     * @param string $method
     * @param array|null $params
     * @param ShipEngineConfig $config
     * @return mixed
     * @throws GuzzleException
     */
    private function sendRPCRequest(string $method, ?array $params, ShipEngineConfig $config)
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
     * @param array|null $params
     * @param int $retry
     * @param ShipEngineConfig $config
     * @return mixed
     * @throws GuzzleException
     */
    private function sendRequest(
        string $method,
        ?array $params,
        int $retry,
        ShipEngineConfig $config
    ) {
        $assert = new Assert();
        $baseUri = !getenv('CLIENT_BASE_URI') ? $config->baseUrl : getenv('CLIENT_BASE_URI');
        $dispatcher = new EventDispatcher();
        $shipengineEventListener = $config->eventListener;
        $requestHeaders = array(
            'Api-Key' => $config->apiKey,
            'User-Agent' => $this->deriveUserAgent(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        );

        $body = $this->wrapRequest($method, $params);

        // Config for the Guzzle Client
        $guzzleConfig = array(
            'baseUri' => $baseUri,
            'headers' => $requestHeaders
        );

        $client = new Client($guzzleConfig);

        $jsonData = json_encode($body, JSON_UNESCAPED_SLASHES);

        $requestSentEvent = new RequestSentEvent(
            "Calling the ShipEngine $method API at $baseUri",
            $body['id'],
            $baseUri,
            $requestHeaders,
            $body,
            $retry,
            $config->timeout
        );

        $dispatcher->addListener(
            $requestSentEvent::REQUEST_SENT,
            [$shipengineEventListener, 'onRequestSent']
        );
        $dispatcher->dispatch($requestSentEvent, $requestSentEvent::REQUEST_SENT);

        $request = new Request('POST', $baseUri, $requestHeaders, $jsonData);

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

        $responseBody = (string)$response->getBody();
        $parsedResponse = json_decode($responseBody, true);
        $statusCode = $response->getStatusCode();

        $assert->isResponse404($statusCode, $parsedResponse);

        $responseReceivedEvent = new ResponseReceivedEvent(
            "Response Received",
            $parsedResponse['id'],
            $baseUri,
            $statusCode,
            $response->getHeaders(),
            $parsedResponse,
            $retry,
            (new \DateTime())->diff($requestSentEvent->timestamp)
        );
        $dispatcher->addListener(
            $responseReceivedEvent::RESPONSE_RECEIVED,
            [$shipengineEventListener, 'onResponseReceived']
        );
        $dispatcher->dispatch($responseReceivedEvent, $responseReceivedEvent::RESPONSE_RECEIVED);

        $assert->doesResponseHave500Error($parsedResponse, $statusCode);

        return $this->handleResponse($parsedResponse);
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
                $retryAfter = $error['data']['retryAfter'] * 1000;
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
