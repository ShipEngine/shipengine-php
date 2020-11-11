<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Http\Client\HttpClient;

class ServiceFactory
{

    public function __construct(HttpClient $client, int $page_size)
    {
    }
}
