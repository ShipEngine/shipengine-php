<?php declare(strict_types=1);

namespace Util\Constants;

use ShipEngine\Util\Constants\RPCMethods;
use PHPUnit\Framework\TestCase;

/**
 * Class RPCMethodsTest
 *
 * @covers \ShipEngine\Util\Constants\RPCMethods;
 * @package Util\Constants
 */
final class RPCMethodsTest extends TestCase
{
    public function testInstantiation()
    {
        $rpc_methods = new RPCMethods();

        $this->assertInstanceOf(RPCMethods::class, $rpc_methods);
        $this->assertIsString($rpc_methods::ADDRESS_VALIDATE);
    }
}
