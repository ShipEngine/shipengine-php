<?php declare(strict_types=1);

namespace Util;

use Psr\Log\LoggerInterface;
use ShipEngine\Util\ShipEngineLogger;
use PHPUnit\Framework\TestCase;

/**
 * Class ShipEngineLoggerTest
 *
 * @covers \ShipEngine\Util\ShipEngineLogger
 * @package Util
 */
final class ShipEngineLoggerTest extends TestCase
{
    public function testLog()
    {
        $logger = new ShipEngineLogger();
        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }
}
