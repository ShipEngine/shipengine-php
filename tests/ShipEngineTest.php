<?php declare(strict_types=1);

namespace ShipEngine\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\ShipEngine;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\Service\ServiceFactory
 */
final class ShipEngineTest extends TestCase
{
    public function testShipEngineConstructor(): void
    {
        $config = new ShipEngineConfig(
            array(
                'api_key' => 'baz'
            )
        );

        $shipengine = new ShipEngine($config);

        $this->assertInstanceOf(ShipEngine::class, $shipengine);
    }
}
