<?php declare(strict_types=1);


namespace ShipEngine\Service\RPCService\Interfaces;

use Psr\Http\Message\RequestInterface as HTTPRequestInterface;

interface RPCRequestInterface extends HTTPRequestInterface
{
    /**
     * @return string
     */
    public function getRpcMethod(): string;


    /**
     * @return string
     */
    public function getRpcParams(): string;

    public function getRpcId();

    public function getRpcVersion();
}