<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Http\Client\Exception\HttpException;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;

use Psr\Http\Message\ResponseInterface;

use ShipEngine\ShipEngineClient;

/**
 * Serialize and send HTTP requests.
 */
abstract class AbstractService
{
    protected ShipEngineClient $client;
    protected MessageFactory $message_factory;

    public function __construct(ShipEngineClient $client)
    {
        $this->client = $client;
        $this->message_factory = MessageFactoryDiscovery::find();
    }
    
    /**
     * Create and send an HTTP request.
     */
    protected function request(string $method, string $path, string $body = null): ResponseInterface
    {
        $request = $this->message_factory->createRequest($method, $path, array(), $body);

        return $this->client->sendRequest($request);
    }
}
