<?php declare(strict_types=1);

namespace ShipEngine\Model\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Exception\ErrorException;
use ShipEngine\Exception\InfoException;
use ShipEngine\Exception\WarningException;
use ShipEngine\Model\Address;
use ShipEngine\Model\AddressQuery;
use ShipEngine\Model\AddressQueryResult;

final class AddressQueryResultTest extends TestCase
{
    public function testExceptionsAreEmpty(): void
    {
        $yankee_stadium = new AddressQuery(['1 E 161 St'], 'The Bronx', 'NY', '10451', 'US');
        $dodger_stadium = new Address(['1000 Elysion Park'], 'Los Angeles', 'CA', '90012', 'US');

        $result = new AddressQueryResult($yankee_stadium, $dodger_stadium);

        $this->assertEmpty($result->exceptions);
    }

    public function testGetInfo(): void
    {
        $wrigley_field = new AddressQuery(['1060 W Addison St'], 'Chicago', 'IL', '60613', 'US');
        $exceptions = array(new InfoException('foo'), new InfoException('bar'));

        $result = new AddressQueryResult($wrigley_field, null, $exceptions);
        
        $this->assertCount(2, $result->info());
    }

    public function testGetWarnings(): void
    {
        $minute_made_park = new AddressQuery(['501 Crawford St'], 'Houston', 'TX', '77002', 'US');
        $exceptions = array(new WarningException('foo/bar'));

        $result = new AddressQueryResult($minute_made_park, null, $exceptions);
        
        $this->assertCount(1, $result->warnings());
    }

    public function testGetErrors(): void
    {
        $chase_field = new AddressQuery(['401 E Jefferson St'], 'Phoenix', 'AZ', '85004', 'US');
        $exceptions = array(new ErrorException('foo'), new ErrorException('bar'), new ErrorException('baz'));

        $result = new AddressQueryResult($chase_field, null, $exceptions);
        
        $this->assertCount(3, $result->errors());
    }
}
