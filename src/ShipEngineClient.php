<?php declare(strict_types=1);

namespace ShipEngine;

use cbschuld\UuidBase58;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ShipEngine\Message\AccountStatusException;
use ShipEngine\Message\BusinessRuleException;
use ShipEngine\Message\SecurityException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\Util\VersionInfo;
use ShipEngine\Util;

/**
 * A wrapped `JSON-RPC 2.0` HTTP client.
 *
 * @pacakge ShipEngine
 */
final class ShipEngineClient
{
    use Util\Getters;

    protected MessageFactory $message_factory;

    /**
     * @var PluginClient
     */
    private PluginClient $client;

    private array $plugins;

    private array $headers;

    private string $base_url;

    private ShipEngineConfig $config;

    /**
     * ShipEngineClient constructor, this is the global client and is used if a custom
     * client is not passed in via configuration options.
     *
     * @param ShipEngineConfig $config
     * @param HttpClient|null $client
     */
    public function __construct(ShipEngineConfig $config, HttpClient $client = null)
    {
        $this->config = $config;

        if (!$client) {
            $client = HttpClientDiscovery::find();
        }

        $this->message_factory = MessageFactoryDiscovery::find();

        $this->headers = array();
        $this->headers['Api-Key'] = $config->api_key;
        $this->headers['User-Agent'] = $this->deriveUserAgent();
        $this->headers['Content-Type'] = 'application/json';

        $uri_factory = UriFactoryDiscovery::find();

        if (!getenv('CLIENT_BASE_URI')) {
            $this->base_url = $config->base_url;
        } else {
            $this->base_url = getenv('CLIENT_BASE_URI');
        }

        $base_uri = $uri_factory->createUri($this->base_url);

        $this->plugins = array();
        $this->plugins[] = new HeaderDefaultsPlugin($this->headers);
        $this->plugins[] = new BaseUriPlugin($base_uri);
        $this->plugins[] = new RetryPlugin([
            'retries' => $config->retries,
            'error_response_decider' => function (RequestInterface $request, ResponseInterface $response): bool {
                $status = $response->getStatusCode();
                return $status === 429;
            },
//            'error_response_delay' => function (
//RequestInterface $request,
// ResponseInterface $response,
// int $retries
//): int {
//                $res = json_decode($response->getBody()->getContents(), true);
//
//                return $res->error->data['retry_after'] * 1000; // number of milliseconds to wait
//            }
        ]);

        $this->client = new PluginClient($client, $this->plugins);
    }

    /**
     * Send a `JSON-RPC 2.0` request via HTTP Messages to ShipEngine API. If the response
     * is successful, the result is returned. Otherwise, an error is thrown.
     *
     * @param RequestInterface $request
     * @param ShipEngineConfig $config
     * @return ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function sendRequest(RequestInterface $request, ShipEngineConfig $config): ResponseInterface
    {

        //TODO: implement the use of the passed in $config
        return $this->client->sendRequest($request);
    }

    /**
     * Wrap request per `JSON-RPC 2.0` spec.
     *
     * @param string $method
     * @param array $params
     */
    private function wrapRequest(string $method, array $params)
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
     * @param string $method Name of an RPC method.
     * @param array $params Data that a remote procedure will make use of.
     * @param ShipEngineConfig $config
     */
    public function request(string $method, array $params, ShipEngineConfig $config)
    {
        $body = $this->wrapRequest($method, $params);

//        $event = new RequestSentEvent(
//            "Calling the ShipEngine {$method} API at {$config->base_url}",
//            $body['id'],
//            $config->base_url,
//            $client->headers,
//            $body,
//            $config->retries,
//
//        );

        $response = $this->sendRPCRequest($body, $config, $this);
        $status_code = $response->getStatusCode();
        $reason_phrase = $response->getReasonPhrase();

        if ($status_code !== 200) {
            throw new ShipEngineException(
                "Address Validation request failed -- status_code: {$status_code} reason: {$reason_phrase}"
            );
        }

        return $this->handleResponse(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * Send a `JSON-RPC 2.0` request via *ShipEngineClient*.
     *
     * @param array $body
     * @param ShipEngineConfig $config
     * @param ShipEngineClient $client
     * @return ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function sendRPCRequest(array $body, ShipEngineConfig $config, ShipEngineClient $client): ResponseInterface
    {
        $jsonData = json_encode($body, JSON_UNESCAPED_SLASHES);

        $request = $this->message_factory->createRequest('POST', 'jsonrpc', array(), $jsonData);

        return $this->sendRequest($request, $config);
    }

    private function handleResponse($response)
    {
        if (isset($response['result']) === true) {
            return $response['result'];
        }

        $error = $response['error'];

        switch ($error['data']['error_type']) {
            case 'account_status':
                throw new AccountStatusException(
                    $error['message'],
                    $response['id'],
                    $error['data']['error_source'],
                    $error['data']['error_type'],
                    $error['data']['error_code']
                );
            case 'security':
                throw new SecurityException(
                    $error['message'],
                    $response['id'],
                    $error['data']['error_source'],
                    $error['data']['error_type'],
                    $error['data']['error_code']
                );
            case 'validation':
                throw new ValidationException(
                    $error['message'],
                    $response['id'],
                    $error['data']['error_source'],
                    $error['data']['error_type'],
                    $error['data']['error_code']
                );
            case 'business_rules':
                throw new BusinessRuleException(
                    $error['message'],
                    $response['id'],
                    $error['data']['error_source'],
                    $error['data']['error_type'],
                    $error['data']['error_code']
                );
            case 'system':
                throw new SystemException(
                    $error['message'],
                    $response['id'],
                    $error['data']['error_source'],
                    $error['data']['error_type'],
                    $error['data']['error_code']
                );
            default:
                throw new ShipEngineException(
                    $error['message'],
                    $response['id'],
                    $error['data']['error_source'],
                    $error['data']['error_type'],
                    $error['data']['error_code']
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
