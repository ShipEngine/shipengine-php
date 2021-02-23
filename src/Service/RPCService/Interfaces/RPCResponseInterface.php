<?php declare(strict_types=1);


namespace ShipEngine\Service\RPCService\Interfaces;

use Psr\Http\Message\ResponseInterface as HTTPResponseInterface;

interface RPCResponseInterface extends HTTPResponseInterface
{
    public function getRpcErrorCode();

    public function getRpcErrorMessage();

    public function getRpcErrorData();

    public function getRpcResult();

    public function getRpcVersion();
}