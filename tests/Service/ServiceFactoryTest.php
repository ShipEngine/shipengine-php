<?php declare(strict_types=1);

namespace ShipEngine\Service\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Service\ServiceFactory;

/**
 * @covers ShipEngine\ShipEngineClient
 * @covers ShipEngine\Service\ServiceFactory
 */
final class ServiceFactoryTest extends TestCase
{
    public function testServiceNotFound(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $api_key = 'baz';
        $user_agent = 'PHP';

        $client = new ShipEngineClient($api_key, $user_agent);
        $factory = new ServiceFactory($client);

        $factory->foo;
    }
}
