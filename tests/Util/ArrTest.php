<?php declare(strict_types=1);

namespace ShipEngine\Util\Tests;

use PHPUnit\Framework\TestCase;

use ShipEngine\Util\Arr;

/**
 * @covers \ShipEngine\Util\Arr::subArray
 */
final class ArrTest extends TestCase
{
    public function testSubArray(): void
    {
        $old = array(
            'one' => 1,
            'two' => 2,
            'three' => 3
        );

        $new = Arr::subArray($old, 'one', 'two');
        $this->assertArrayHasKey('one', $new);
        $this->assertArrayHasKey('two', $new);
        $this->assertNull($new['three']);
    }
}
