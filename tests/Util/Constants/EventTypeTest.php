<?php declare(strict_types=1);

namespace Util\Constants;

use ShipEngine\Util\Constants\EventType;
use PHPUnit\Framework\TestCase;

/**
 * Class EventTypeTest
 *
 * @covers \ShipEngine\Util\Constants\EventType;
 * @package Util\Constants
 */
final class EventTypeTest extends TestCase
{
    public function testInstantiation()
    {
        $event_type = new EventType();

        $this->assertInstanceOf(EventType::class, $event_type);
        $this->assertIsString($event_type::REQUEST_SENT);
    }
}
