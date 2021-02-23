<?php declare(strict_types=1);


namespace ShipEngine\Service\RPCService;

use ShipEngine\Service\RPCService\Interfaces\RPCResponseInterface;

abstract class RPCResponse implements RPCResponseInterface
{
    public function getRpcErrorCode(): ?string
    {
        $error = $this->getKeyFromResponseBody('error');

        return isset($error['code']) ? $error['code'] : null;
    }

    public function getRpcErrorMessage(): ?string
    {
        $error = $this->getKeyFromResponseBody('error');

        return isset($error['message']) ? $error['message'] : null;
    }

    public function getRpcErrorData(): ?string
    {
        $error = $this->getKeyFromResponseBody('error');

        return isset($error['data']) ? $error['data'] : null;
    }

    public function getRpcId(): string
    {
        return $this->getKeyFromResponseBody('id');
    }

    public function getRpcResult(): string
    {
        $this->getKeyFromResponseBody('result');
    }

    public function getRpcVersion(): string
    {
        $this->getKeyFromResponseBody('jsonrpc');
    }

    protected function getKeyFromResponseBody(string $key)
    {
        $rpcResponseBody = json_decode((string) $this->getBody(), true);
        return isset($rpcResponseBody[$key]) ? $rpcResponseBody[$key] : null;
    }
}