<?php declare(strict_types=1);

namespace Util;

use ShipEngine\Util\VersionInfo;
use PHPUnit\Framework\TestCase;

/**
 * Class VersionInfoTest
 *
 * @covers ShipEngine\Util\VersionInfo
 * @package Util
 */
final class VersionInfoTest extends TestCase
{
    public function testInstantiation()
    {
        $version = new VersionInfo();

        $this->assertInstanceOf(VersionInfo::class, $version);
        $this->assertIsString($version::string());
    }
}
