<?php declare(strict_types=1);

namespace ShipEngine\Service\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Service\ServiceFactory;

/**
 * @covers ShipEngine\ShipEngineClient
 * @covers ShipEngine\ShipEngineConfig
 * @covers ShipEngine\Service\ServiceFactory
 */
final class ServiceFactoryTest extends TestCase
{
    public function testServiceNotFound(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $config = new ShipEngineConfig(['api_key' => 'FOO', 'user_agent' => 'BAR']);
        $client = new ShipEngineClient($config);
        $factory = new ServiceFactory($client);

        $factory->foo;
    }
}
