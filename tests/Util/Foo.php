<?php declare(strict_types=1);

namespace ShipEngine\Util\Tests;

use ShipEngine\Util\Getters;

final class Foo implements \JsonSerializable
{
    use Getters;

    private string $bar = 'baz';

    public function jsonSerialize()
    {
        return array('0' => 0, '1' => 1);
    }
}
