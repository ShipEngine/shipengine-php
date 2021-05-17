<?php declare(strict_types=1);

namespace ShipEngine;

use PHPUnit\Framework\TestCase;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 */
final class ShipEngineTest extends TestCase
{
    private static ShipEngine $shipengine;

    public static function setUpBeforeClass(): void
    {
        self::$shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz',
                'baseUrl' => 'https://api.shipengine.com',
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
                'events' => null
            )
        );
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(ShipEngine::class, self::$shipengine);
    }
}
