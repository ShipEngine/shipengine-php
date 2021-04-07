<?php declare(strict_types=1);

namespace ShipEngine\Service;

use cbschuld\UuidBase58;
use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Message\ResponseInterface;
use ShipEngine\Message\AccountStatusException;
use ShipEngine\Message\BusinessRuleException;
use ShipEngine\Message\SecurityException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\ValidationException;
use ShipEngine\ShipEngineClient;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Serialize and send RPC requests over HTTP messages.
 *
 * @package ShipEngine\Service
 */
abstract class AbstractService
{
    /**
     * ShipEngine HTTP Client - used to make all HTTP Requests.
     *
     * @var ShipEngineClient
     */
    protected ShipEngineClient $client;

    /**
     *
     *
     * @var MessageFactory
     */
    protected MessageFactory $message_factory;

    /**
     * @var string
     */
    private const RPC_PATH = '/rpc';

    /**
     * @var string
     */
    private const HTTP_METHOD = 'POST';

    /**
     * AbstractService constructor.
     *
     * @param ShipEngineClient $client
     * @throws NotFoundException
     */
    public function __construct(ShipEngineClient $client)
    {
        $this->client = $client;
        $this->message_factory = MessageFactoryDiscovery::find();
    }

    /**
     * Create and send a `JSON-RPC 2.0` request over HTTP messages.
     *
     * @param string $method Name of an RPC method.
     * @param array $params Data that a remote procedure will make use of.
     * @param ShipEngineConfig $config
     */
    protected function request(string $method, array $params, ShipEngineConfig $config)
    {
        $body = $this->wrapRequest($method, $params);
        $response = $this->sendRequest($body, $config);
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
     * Send a `JSON-RPC 2.0` request via *ShipEngineClient*.
     *
     * @param array $body
     * @param ShipEngineConfig $config
     * @return ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function sendRequest(array $body, ShipEngineConfig $config): ResponseInterface
    {
        $jsonData = json_encode($body, JSON_UNESCAPED_SLASHES);

        $request = $this->message_factory->createRequest(self::HTTP_METHOD, self::RPC_PATH, array(), $jsonData);

        return $this->client->sendRequest($request, $config);
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
}
