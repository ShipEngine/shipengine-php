<?php declare(strict_types=1);

namespace ShipEngine\Util\Tests;

use PHPUnit\Framework\TestCase;

use ShipEngine\Util\Getters;

final class Foo
{
    use Getters;

    private string $bar = 'baz';
}

/**
 * @covers \ShipEngine\Util\Getters
 */
final class GettersTest extends TestCase
{
    public function testFoundGetter(): void
    {
        $foo = new Foo();
        $this->assertEquals('baz', $foo->bar);
    }

    public function testUnfoundGetter(): void
    {
        $this->expectException(\RuntimeException::class);
        $foo = new Foo();
        $foo->baz;
    }
}
