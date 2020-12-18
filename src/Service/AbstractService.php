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
     *
     */
    private function jsonize(\JsonSerializable $obj, array $keys)
    {
        $json = $obj->jsonSerialize();
        foreach ($keys as $key) {
            $old = $key[0];
            $new = $key[1];
            $json[$new] = $json[$old];
            unset($json[$old]);
        }
        return $json;
    }
    
    /**
     *
     */
    protected function encode(\JsonSerializable $obj, array ...$keys): string
    {
        $json = $this->jsonize($obj, $keys);
        return json_encode($json);
    }

    /**
     *
     */
    protected function encodeArray(array $objs, array ...$keys): string
    {
        foreach ($objs as $obj) {
            $obj = $this->jsonize($obj, $keys);
        }
        return json_encode($objs);
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
