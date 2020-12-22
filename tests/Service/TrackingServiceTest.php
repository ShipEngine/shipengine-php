<?php declare(strict_types=1);

namespace ShipEngine\Service\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Model\Tracking\Query;
use ShipEngine\Model\Tracking\QueryResult;
use ShipEngine\ShipEngine;

/**
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
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\ShipEngineConfig
 * @covers \ShipEngine\Util\Getters
 * @covers \ShipEngine\Util\Arr::flatten
 * @covers \ShipEngine\Util\Arr::subArray
 * @covers \ShipEngine\Util\ISOString
 */
final class TrackingServiceTest extends TestCase
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

    public function testTrackingQuery(): void
    {
        // v1/tracking?carrier_code=query&tracking_number=foobar
        $query = new Query('query', 'foobar');
        $result = $this->shipengine->tracking->query($query);

        $this->assertEmpty($result->errors());
        $this->assertCount(3, $result->information->events);
    }

    public function testTrackingQueryError(): void
    {
        $query = new Query('foobar', 'foobar');
        $result = $this->shipengine->tracking->query($query);

        $this->assertNull($result->information);
        $this->assertNotEmpty($result->errors());
    }
    
    public function testLabel(): void
    {
        // v1/labels/label/track
        $result = $this->shipengine->tracking->query("label");

        $this->assertEmpty($result->errors());
        $this->assertNotEmpty($result->information);
    }
    
    public function testLabelError(): void
    {
        // v1/labels/error/track
        $result = $this->shipengine->tracking->query("error");

        $this->assertNotEmpty($result->errors());
        $this->assertEquals('HTTP ERROR: 404', $result->errors()[0]->getMessage());
    }
}
