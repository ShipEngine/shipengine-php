<?php declare(strict_types=1);

namespace ShipEngine\Message\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Message\Error;
use ShipEngine\Message\Info;
use ShipEngine\Message\Warning;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\Query;
use ShipEngine\Model\Address\QueryResult;

/**
 * @covers \ShipEngine\Message\Message
 * @covers \ShipEngine\Message\Wrapper
 * @covers \ShipEngine\Model\Address\Address
 * @covers \ShipEngine\Model\Address\Query
 * @covers \ShipEngine\Model\Address\QueryResult
 */
final class WrapperTest extends TestCase
{
    public function testMessagesAreEmpty(): void
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
        $messages = array(new Info('foo'), new Info('bar'));

        $result = new QueryResult($wrigley_field, null, $messages);
        
        $this->assertCount(2, $result->info());
    }

    public function testGetWarnings(): void
    {
        $minute_made_park = new Query(['501 Crawford St'], 'Houston', 'TX', '77002', 'US');
        $messages = array(new Warning('foo/bar'));

        $result = new QueryResult($minute_made_park, null, $messages);

        $this->assertCount(1, $result->warnings());
    }

    public function testGetErrors(): void
    {
        $chase_field = new Query(['401 E Jefferson St'], 'Phoenix', 'AZ', '85004', 'US');
        $messages = array(new Error('foo'), new Error('bar'), new Error('baz'));

        $result = new QueryResult($chase_field, null, $messages);
        
        $this->assertCount(3, $result->errors());
    }
}
