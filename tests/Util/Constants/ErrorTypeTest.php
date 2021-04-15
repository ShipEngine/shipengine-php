<?php declare(strict_types=1);

namespace Util\Constants;

use ShipEngine\Util\Constants\ErrorType;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorTypeTest
 *
 * @covers \ShipEngine\Util\Constants\ErrorType
 * @package Util\Constants
 */
final class ErrorTypeTest extends TestCase
{
    public function testInstantiation()
    {
        $error_type = new ErrorType();

        $this->assertInstanceOf(ErrorType::class, $error_type);
        $this->assertIsString($error_type::VALIDATION);
    }
}
