<?php declare(strict_types=1);

namespace Service\Package;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Package\TrackingData;
use ShipEngine\ShipEngine;

/**
 * Class PackageTrackingTraitTest
 *
 * @covers \ShipEngine\Service\ShipEngine;
 * @covers \ShipEngine\Service\ShipEngineClient;
 * @covers \ShipEngine\Service\AbstractService;
 * @covers \ShipEngine\Service\ServiceFactory;
 * @covers \ShipEngine\Service\Package\PackageTrackingService;
 * @covers \ShipEngine\Service\Package\PackageTrackingTrait;
 * @covers \ShipEngine\Model\Package\PackageTrackingResult;
 * @covers \ShipEngine\Model\Package\PackageTrackingParams;
 */
final class PackageTrackingTraitTest extends TestCase
{
    /**
     * @var ShipEngine
     */
    private static ShipEngine $shipengine;

    /**
     * @var string
     */
    private static string $carrier_code;

    /**
     * @var string
     */
    private static string $tracking_number;

    /**
     * Import `simengine/rpc/rpc.json` into *Hoverfly* before class instantiation.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$shipengine = new ShipEngine('baz');
        self::$carrier_code = 'stamps.com';
        self::$tracking_number = 'abc123';
    }

    public function testTrackPackage(): void
    {
        $tracking_data = self::$shipengine->trackPackage(self::$carrier_code, self::$tracking_number);

        $this->assertInstanceOf(TrackingData::class, $tracking_data);
    }
}
