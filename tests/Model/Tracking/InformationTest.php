<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Util\ISOString;

use ShipEngine\Model\Tracking\Event;
use ShipEngine\Model\Tracking\Information;
use ShipEngine\Model\Tracking\Status;

/**
 * @covers ShipEngine\Util\ISOString
 * @covers ShipEngine\Model\Tracking\Event
 * @covers ShipEngine\Model\Tracking\Information
 * @covers ShipEngine\Model\Tracking\Status
 */
final class InformationTest extends TestCase
{
    private Information $information;
    
    protected function setUp(): void
    {
        $events[] = new Event(
            new ISOString("2020-01-02T00:00:00Z"),
            Status::DELIVERED,
            "foo",
            "foo",
            "foo",
            []
        );
        $events[] = new Event(
            new ISOString("2020-01-01T05:00:00Z"),
            Status::IN_TRANSIT,
            "foo",
            "foo",
            "foo",
            []
        );
        $events[] = new Event(
            new ISOString("2020-01-01T00:00+05:00"),
            Status::ACCEPTED,
            "foo",
            "foo",
            "foo",
            []
        );

        $this->information = new Information("abc", "123", new ISOString("2020-01-01T00:00:00Z"), $events);
    }
    
    public function testEventsAreSorted(): void
    {
        $this->assertEquals(Status::ACCEPTED, $this->information->events[0]->status);
    }

    public function testLatestEvent(): void
    {
        $this->assertEquals(Status::DELIVERED, $this->information->latestEvent()->status);
    }

    public function testShippedAt(): void
    {
        $this->assertEquals("2020-01-01T00:00+05:00", (string) $this->information->shippedAt());
    }

    public function testDeliveredAt(): void
    {
        $this->assertEquals("2020-01-02T00:00:00Z", (string) $this->information->deliveredAt());
    }
}
