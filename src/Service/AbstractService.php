<?php declare(strict_types=1);

namespace ShipEngine\Service;

use cbschuld\UuidBase58;
use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Message\ResponseInterface;
use ShipEngine\ShipEngineClient;

/**
 * Serialize and send RPC requests over HTTP messages.
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
     * AbstractService constructor.
     * @param ShipEngineClient $client
     * @throws NotFoundException
     */
    public function __construct(ShipEngineClient $client)
    {
        $this->client = $client;
        $this->message_factory = MessageFactoryDiscovery::find();
    }

    /**
     * Create and send an RPC request over HTTP messages.
     *
     * @param string $method Name of an RPC method.
     * @param array $params Data that a remote procedure will make use of.
     * @return ResponseInterface
     */
    protected function request(string $method, array $params): ResponseInterface
    {
        $HTTP_METHOD = 'POST';

        $jsonData = json_encode(array_filter([
            'id' => UuidBase58::id(),
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params
        ]));

        $request = $this->message_factory->createRequest($HTTP_METHOD, self::RPC_PATH, array(), $jsonData);

        return $this->client->sendRequest($request);
    }
}
