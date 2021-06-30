<?php declare(strict_types=1);

namespace ShipEngine;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Package\TrackPackageResult;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @uses \ShipEngine\ShipEngineConfig
 * @uses \ShipEngine\Util\Assert
 * @uses \ShipEngine\Service\Package\TrackPackageService
 * @uses \ShipEngine\Model\Package\TrackPackageResult
 */
final class ShipEngineTest extends TestCase
{
    public function testInstantiation(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => 'https://api.shipengine.com',
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
                'events' => null
            )
        );
        $this->assertInstanceOf(ShipEngine::class, $shipengine);
    }
}
