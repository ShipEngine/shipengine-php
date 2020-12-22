<?php declare(strict_types=1);

namespace ShipEngine\Service\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Message\Error;
use ShipEngine\Model\Tracking\Query;
use ShipEngine\ShipEngine;

/**
 * @covers \ShipEngine\Message\Error
 * @covers \ShipEngine\Message\Message
 * @covers \ShipEngine\Message\Wrapper
 * @covers \ShipEngine\Model\Tracking\Event
 * @covers \ShipEngine\Model\Tracking\Information
 * @covers \ShipEngine\Model\Tracking\Location
 * @covers \ShipEngine\Model\Tracking\Query
 * @covers \ShipEngine\Model\Tracking\QueryResult
 * @covers \ShipEngine\Service\AbstractService
 * @covers \ShipEngine\Service\ServiceFactory
 * @covers \ShipEngine\Service\TrackingService
 * @covers \ShipEngine\Service\TrackingTrait
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\ShipEngineConfig
 * @covers \ShipEngine\Util\Arr
 * @covers \Shipengine\Util\ISOString
 */
final class TrackingTraitTest extends TestCase
{
    private ShipEngine $shipengine;

    public static function setUpBeforeClass(): void
    {
        exec('hoverctl import simengine/v1/tracking.json');
    }
        
    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/v1/tracking.json');
    }
    
    protected function setUp(): void
    {
        $this->shipengine = new ShipEngine(['api_key' => 'foobar', 'base_uri' => 'http://localhost:8500/v1']);
    }

    public function testTrackShipmentQueryArgs(): void
    {
        // /tracking?carrier_code=query&tracking_number=foobar
        $information = $this->shipengine->trackShipment("query", "foobar");

        $this->assertEquals("foobar", $information->tracking_number);
    }

    public function testTrackShipmentQuery(): void
    {
        // /tracking?carrier_code=query&tracking_number=foobar
        $query = new Query("query", "foobar");
        $information = $this->shipengine->trackShipment($query);

        $this->assertEquals("foobar", $information->tracking_number);
    }

    public function testTrackShipmentPackageID(): void
    {
        // /labels/label/track
        $information = $this->shipengine->trackShipment("label");

        $this->assertEquals("label", $information->tracking_number);
    }

    public function testTrackShipmentException(): void
    {
        $this->expectException(Error::class);

        $information = $this->shipengine->trackShipment("error");
    }
}
