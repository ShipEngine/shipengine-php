<?php  declare(strict_types=1);


namespace ShipEngine\Service\RPCService\Util;

use ShipEngine\Service\RPCService\Interfaces\RPCRequestInterface;

abstract class RPCRequest implements RPCRequestInterface
{
    public function getRpcId(): string
    {
        return $this->getKeyFromRequestBody('id');
    }

    public function getRpcMethod(): string
    {
        return $this->getKeyFromRequestBody('method');
    }

    public function getRpcParams(): string
    {
        return $this->getKeyFromRequestBody('params');
    }

    public function getRpcVersion(): string
    {
        return $this->getKeyFromRequestBody('jsonrpc');
    }

    protected function getKeyFromRequestBody(string $key)
    {
        $rpcRequestBody = json_decode((string) $this->getBody(), true);
        $rpcRequestBodyType = gettype($rpcRequestBody);
        echo "Parsed RPC Body Type: {$rpcRequestBodyType}";
        return isset($rpcRequestBody[$key]) ? $rpcRequestBody[$key] : null;
    }
}