<?php declare(strict_types=1);

namespace ShipEngine\Model\Address\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Model\Address\Query;

/**
 * @covers \ShipEngine\Model\Address\Query
 */
final class QueryTest extends TestCase
{
    public function testNullProperties(): void
    {
        $wrigley_field = new Query(['1060 W Addison St']);

        $this->assertNull($wrigley_field->city_locality);
    }
}
