<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Http\Client\HttpClient;

abstract class AbstractService
{
    protected HttpClient $client;

    public function __construct(HttpClient $client)
    {
    }
}
