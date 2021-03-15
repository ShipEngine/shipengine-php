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
        exec('hoverctl import simengine/rpc/rpc.json');

        self::$shipengine = new ShipEngine('baz');
        self::$carrier_code = 'stamps.com';
        self::$tracking_number = 'abc123';
    }

    /**
     * Delete `simengine/rpc/rpc.json` from *Hoverfly*.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/rpc/rpc.json');
    }

    public function testTrackPackage(): void
    {
        $tracking_data = self::$shipengine->trackPackage(self::$carrier_code, self::$tracking_number);

        $this->assertInstanceOf(TrackingData::class, $tracking_data);
    }
}
