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

    protected function jsonize(\JsonSerializable $obj, array ...$keys): string
    {
        $json = $obj->jsonSerialize();
        foreach ($keys as $key) {
            $old = $key[0];
            $new = $key[1];
            $json[$new] = $json[$old];
            unset($json[$old]);
        }
        return json_encode($json);
    }

    protected function request(string $method, string $path, string $body = null)
    {
        $request = $this->message_factory->createRequest($method, $path, array(), $body);
        return $this->client->sendRequest($request);
    }
}
