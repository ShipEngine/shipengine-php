<?php declare(strict_types=1);

namespace ShipEngine\Model\Address\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Exception\ErrorException;
use ShipEngine\Exception\InfoException;
use ShipEngine\Exception\WarningException;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\Query;
use ShipEngine\Model\Address\QueryResult;

/**
 * @covers \ShipEngine\Exception\Wrapper
 * @covers \ShipEngine\Model\Address\Address
 * @covers \ShipEngine\Model\Address\Query
 * @covers \ShipEngine\Model\Address\QueryResult
 */
final class WrapperTest extends TestCase
{
    public function testExceptionsAreEmpty(): void
    {
        $yankee_stadium = new Query(['1 E 161 St'], 'The Bronx', 'NY', '10451', 'US');
        $dodger_stadium = new Address(['1000 Elysion Park'], 'Los Angeles', 'CA', '90012', 'US');

        $result = new QueryResult($yankee_stadium, $dodger_stadium);

        $this->assertEmpty($result->errors());
        $this->assertEmpty($result->info());
        $this->assertEmpty($result->warnings());
    }

    public function testGetInfo(): void
    {
        $wrigley_field = new Query(['1060 W Addison St'], 'Chicago', 'IL', '60613', 'US');
        $exceptions = array(new InfoException('foo'), new InfoException('bar'));

        $result = new QueryResult($wrigley_field, null, $exceptions);
        
        $this->assertCount(2, $result->info());
    }

    public function testGetWarnings(): void
    {
        $minute_made_park = new Query(['501 Crawford St'], 'Houston', 'TX', '77002', 'US');
        $exceptions = array(new WarningException('foo/bar'));

        $result = new QueryResult($minute_made_park, null, $exceptions);

        $this->assertCount(1, $result->warnings());
    }

    public function testGetErrors(): void
    {
        $chase_field = new Query(['401 E Jefferson St'], 'Phoenix', 'AZ', '85004', 'US');
        $exceptions = array(new ErrorException('foo'), new ErrorException('bar'), new ErrorException('baz'));

        $result = new QueryResult($chase_field, null, $exceptions);
        
        $this->assertCount(3, $result->errors());
    }
}
