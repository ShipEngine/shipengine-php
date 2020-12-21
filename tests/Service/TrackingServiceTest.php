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
        $query = new Query('fedex', 'foobar');
        $result = $this->shipengine->tracking->query($query);

        $this->assertEmpty($result->errors());
    }
}
