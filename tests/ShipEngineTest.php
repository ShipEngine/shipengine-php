<?php declare(strict_types=1);

namespace ShipEngine\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngine;
use ShipEngine\ShipEngineConfig;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\Service\ServiceFactory
 */
final class ShipEngineTest extends TestCase
{
    public function testShipEngineConstructor(): void
    {
        $api_key = 'FOO/BAR';

        $shipengine = new ShipEngine($api_key);
        
        $this->assertInstanceOf(ShipEngine::class, $shipengine);
    }
}
