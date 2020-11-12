<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;

use ShipEngine\ShipEngineClient;

abstract class AbstractService
{
    protected ShipEngineClient $client;
    protected MessageFactory $message_factory;

    public function __construct(ShipEngineClient $client)
    {
        $this->client = $client;
        $this->message_factory = MessageFactoryDiscovery::find();
    }

    public function request(string $method, string $path)
    {
        $request = $this->message_factory->createRequest($method, $path);
        return $this->client->sendRequest($request);
    }
}
