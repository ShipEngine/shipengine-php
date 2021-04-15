<?php declare(strict_types=1);

namespace Util\Constants;

use ShipEngine\Util\Constants\ErrorCode;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorCodeTest
 *
 * @covers \ShipEngine\Util\Constants\ErrorCode
 * @package Util\Constants
 */
final class ErrorCodeTest extends TestCase
{
    public function testInstantiation()
    {
        $err_code = new ErrorCode();
        $this->assertInstanceOf(ErrorCode::class, $err_code);
        $this->assertIsString($err_code::FIELD_VALUE_REQUIRED);
    }
}
