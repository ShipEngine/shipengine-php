<?php declare(strict_types=1);

namespace ShipEngine\Service;

use cbschuld\UuidBase58;
use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Message\ResponseInterface;
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
     * @var ShipEngineClient
     */
    protected ShipEngineClient $client;

    /**
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
    private const RPC_METHOD = 'POST';

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
     * @return ResponseInterface
     */
    protected function request(string $method, array $params): ResponseInterface
    {
        $body = $this->wrapRequest($method, $params);

        return $this->sendRequest($body);
    }

    /**
     * Wrap request per `JSON-RPC 2.0` spec.
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    private function wrapRequest(string $method, array $params)
    {
        return array_filter([
            'id' => UuidBase58::id(),
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params
        ]);
    }

    /**
     * Create and wrap a batch `JSON-RPC 2.0` request.
     *
     * @param string $method
     * @param array $batch
     * @return ResponseInterface
     */
    protected function batchRequest(string $method, array $batch): ResponseInterface
    {
        $body = $this->wrapBatchRequest($method, $batch);

        return $this->sendRequest($body);
    }


    /**
     * Wrap `batch` request per *JSON-RPC 2.0* spec.
     *
     * @param string $method
     * @param array $batch
     * @return array
     */
    private function wrapBatchRequest(string $method, array $batch): array
    {
        foreach ($batch as &$item) {
            $item = $this->wrapRequest($method, (array)$item);
        }

        return $batch;
    }

    /**
     * Send a `JSON-RPC 2.0` request via *ShipEngineClient*.
     *
     * @param array $body
     * @return ResponseInterface
     */
    private function sendRequest(array $body): ResponseInterface
    {
        $jsonData = json_encode($body);

        $request = $this->message_factory->createRequest(self::RPC_METHOD, self::RPC_PATH, array(), $jsonData);

        return $this->client->sendRequest($request);
    }
}
