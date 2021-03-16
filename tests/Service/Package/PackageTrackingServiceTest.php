<?php declare(strict_types=1);

namespace Service\Package;

use ShipEngine\Model\Package\PackageTrackingParams;
use ShipEngine\Model\Package\PackageTrackingResult;
use ShipEngine\Service\Package\PackageTrackingService;
use PHPUnit\Framework\TestCase;
use ShipEngine\ShipEngine;

/**
 * Tests the method provided in the `PackageTrackingService` that allows
 * obtaining tracking data for a single package.
 *
 * @covers \ShipEngine\Service\ShipEngine;
 * @covers \ShipEngine\Service\ShipEngineClient;
 * @covers \ShipEngine\Service\AbstractService;
 * @covers \ShipEngine\Service\ServiceFactory;
 * @covers \ShipEngine\Service\Package\PackageTrackingService;
 * @covers \ShipEngine\Model\Package\PackageTrackingResult;
 * @covers \ShipEngine\Model\Package\PackageTrackingParams;
 * */
final class PackageTrackingServiceTest extends TestCase
{
    private static ShipEngine $shipengine;

    private static PackageTrackingParams $good_tracking_params;

    /**
     * Import `simengine/rpc/rpc.json` into *Hoverfly* before class instantiation.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$good_tracking_params = new PackageTrackingParams(
            'ups',
            'abc123'
        );
        self::$shipengine = new ShipEngine('baz');
    }

    public function testTrackMethod(): void
    {
        $tracking_data = self::$shipengine->tracking->track(self::$good_tracking_params);

        $this->assertInstanceOf(PackageTrackingResult::class, $tracking_data);
    }
}
