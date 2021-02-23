<?php declare(strict_types=1);


namespace ShipEngine\Service\RPCService\Interfaces;

use Psr\Http\Message\ResponseInterface;
use ShipEngine\Service\RPCService\Util\RPCRequest;

interface RPCClientInterface
{
    const JSON_RPC_SPEC = '2.0';

    public function request(string $id, string $method, array $params = null): ResponseInterface;

    public function notification(string $method, array $params = null): ResponseInterface;

    public function send(RPCRequest $request);

    public function sendConcurrent(RPCRequest $request);

    public function sendAllRequests(RPCRequest $request);

    public function sendAllRequestsConcurrent(array $requests);
}