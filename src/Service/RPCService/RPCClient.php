<?php declare(strict_types=1);


namespace ShipEngine\Service\RPCService;

use Psr\Http\Message\RequestInterface;

use Psr\Http\Message\ResponseInterface;
use ShipEngine\Service\RPCService\Interfaces\RPCClientInterface;
use ShipEngine\Service\AbstractService;

class RPCClient extends AbstractService implements RPCClientInterface
{

    public function request(string $id, string $method, array $params = null): ResponseInterface
    {
        $jsonData = json_encode(array_filter([
            'id' => $id,
            'jsonrpc' => $method,
            'method' => $method,
            'params' => $params
        ]));

        return $this->request('POST', '/', $jsonData);
    }

    public function notification(string $method, array $params = null): ResponseInterface
    {
        // TODO: Implement notification() method.
    }

    public function send(RequestInterface $request)
    {
        // TODO: Implement send() method.
    }

    public function sendConcurrent(RequestInterface $request)
    {
        // TODO: Implement sendConcurrent() method.
    }

    public function sendAllRequests(RequestInterface $request)
    {
        // TODO: Implement sendAllRequests() method.
    }

    public function sendAllRequestsConcurrent(array $requests)
    {
        // TODO: Implement sendAllRequestsConcurrent() method.
    }

    private function
}