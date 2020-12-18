<?php declare(strict_types=1);

namespace ShipEngine\Service\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Model\Tracking\Query;
use ShipEngine\Model\Tracking\QueryResult;
use ShipEngine\ShipEngine;

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
        $query = new Query('usps', 'foobar');
        $result = $this->shipengine->tracking->query($query);

        var_dump($result->errors());
        
        $this->assertEmpty($result->errors());
    }
}
