<?php declare(strict_types=1);

namespace ShipEngine\Model\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Model\AddressQuery;

/**
 * @covers \ShipEngine\Model\AddressQuery
 */
final class AddressQueryTest extends TestCase
{
    public function testNullProperties(): void
    {
        $wrigley_field = new AddressQuery(['1060 W Addison St']);

        $this->assertNull($wrigley_field->city_locality);
    }
}
