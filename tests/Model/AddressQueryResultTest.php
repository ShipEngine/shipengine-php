<?php declare(strict_types=1);

namespace ShipEngine\Model\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Model\Address;
use ShipEngine\Model\AddressQuery;
use ShipEngine\Model\AddressQueryResult;

final class AddressQueryResultTest extends TestCase
{
    public function testExceptionsAreEmpty(): void
    {
        $minute_made_park = new AddressQuery(['501 Crawford St'], 'Houston', 'TX', '77002', 'US');
        $chase_field = new Address(['401 E Jefferson St'], 'Phoenix', 'AZ', '85004', 'US');

        $address_query_result = new AddressQueryResult($minute_made_park, $chase_field);

        $this->assertEmpty($address_query_result->exceptions);
    }

    public function testGetInfo(): void
    {
    }

    public function testGetWarnings(): void
    {
    }

    public function testGetErrors(): void
    {
    }
}
