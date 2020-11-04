<?php declare(strict_types=1);

namespace ShipEngine;

/**
 *
 */
final class Client
{

    private $config;

    /**
     *
     */
    private function __construct(Config $config)
    {
        $this->config = $config;
    }
}
