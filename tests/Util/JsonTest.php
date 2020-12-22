<?php declare(strict_types=1);

namespace ShipEngine\Util\Tests;

use PHPUnit\Framework\TestCase;

use ShipEngine\Util\Json;
use ShipEngine\Util\Tests\Foo;

/**
 * @covers \ShipEngine\Util\JSON::encode
 * @covers \ShipEngine\Util\JSON::encodeArray
 * @covers \ShipEngine\Util\JSON::jsonize
 */
final class JsonTest extends TestCase
{
    public function testEncode(): void
    {
        $foo = new Foo();
        
        $json_string = Json::encode($foo, ['1', 'one']);

        $this->assertEquals('{"0":0,"one":1}', $json_string);
    }

    public function testEncodeArray(): void
    {
        $foos = array(new Foo(), new Foo());

        $json_string = Json::encodeArray($foos, ['0', 'zero']);

        $this->assertEquals('[{"1":1,"zero":0},{"1":1,"zero":0}]', $json_string);
    }
}
