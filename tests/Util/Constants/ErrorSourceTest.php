<?php declare(strict_types=1);

namespace Util\Constants;

use ShipEngine\Util\Constants\ErrorSource;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorSourceTest
 *
 * @covers \ShipEngine\Util\Constants\ErrorSource
 * @package Util\Constants
 */
final class ErrorSourceTest extends TestCase
{
    public function testInstantiation()
    {
        $err_source = new ErrorSource();

        $this->assertInstanceOf(ErrorSource::class, $err_source);
        $this->assertIsString($err_source::SHIPENGINE);
    }
}
