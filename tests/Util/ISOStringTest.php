<?php declare(strict_types=1);

namespace ShipEngine\Util\Tests;

use PHPUnit\Framework\TestCase;

use ShipEngine\Util\ISOString;

/**
 * @covers \ShipEngine\Util\ISOString
 */
final class ISOStringTest extends TestCase
{
    public function testCastToString(): void
    {
        $str = new ISOString('foo bar');
        $strs = explode(' ', (string) $str);

        $this->assertCount(2, $strs);
    }

    public function testHasTime(): void
    {
        $str = new ISOString('2020-01-01T00:00:00Z');
        $this->assertTrue($str->hasTime());

        $str = new ISOString('2020-01-01');
        $this->assertFalse($str->hasTime());
    }

    public function testHasTimezone(): void
    {
        $str = new ISOString('2020-01-01T00:00:00Z');
        $this->assertTrue($str->hasTimezone());

        $str = new ISOString('2020-01-01T00:00:00+00:00');
        $this->assertTrue($str->hasTimezone());

        $str = new ISOString('2020-01-01T00:00:00-00:00');
        $this->assertTrue($str->hasTimezone());
        
        $str = new ISOString('2020-01-01');
        $this->assertFalse($str->hasTimezone());

        $str = new ISOString('2020-01-01T00:00:00');
        $this->assertFalse($str->hasTimezone());
    }
}
