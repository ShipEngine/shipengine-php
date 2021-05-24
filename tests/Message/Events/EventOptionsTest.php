<?php declare(strict_types=1);

namespace Message\Events;

use ShipEngine\Message\Events\EventOptions;
use PHPUnit\Framework\TestCase;

/**
 * Class EventOptionsTest
 *
 * @covers \ShipEngine\Message\Events\EventOptions
 */
final class EventOptionsTest extends TestCase
{
    /**
     * Test instantiation of the **EventOptions** object.
     */
    public function testConstruct(): void
    {
        $requestEventData = new EventOptions([
            'test event options message',
            'id',
            'baseUri',
            'requestHeaders',
            'body',
            'retry',
            'timeout'
        ]);

        $this->assertInstanceOf(EventOptions::class, $requestEventData);
    }
}
