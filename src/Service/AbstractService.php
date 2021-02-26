<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Http\Client\Exception\HttpException;
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
    private const JSON_RPC_SPEC = '2.0';

    /**
     * @var string
     */
    private const RPC_PATH = '/';

    /**
     * AbstractService constructor.
     * @param ShipEngineClient $client
     */
    public function __construct(ShipEngineClient $client)
    {
        $this->client = $client;
        $this->message_factory = MessageFactoryDiscovery::find();
    }

    /**
     * Create and send an RPC request over HTTP messages..
     *
     * @param string $method name of an RPC method
     * @param array $params data that a remote procedure will make use of
     * @return ResponseInterface
     */
    protected function request(string $method, array $params): ResponseInterface
    {
        $HTTP_METHOD = 'POST';

        $jsonData = json_encode(array_filter([
            'id' => $this->generateId(),
            'jsonrpc' => self::JSON_RPC_SPEC,
            'method' => $method,
            'params' => $params
        ]));

        $request = $this->message_factory->createRequest($HTTP_METHOD, self::RPC_PATH, array(), $jsonData);

        return $this->client->sendRequest($request);
    }

    //Using a variant of the third proposed solution for a cryptographically secure ID generator
    // - https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
    /**
     * Generate a cryptographically secure ID.
     *
     * @param null $data
     * @return string
     */
    private function generateId($data = null): string
    {
        try {
            $data ??= random_bytes(16);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
