<?php declare(strict_types=1);

namespace ShipEngine\Model\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Model\Address;

final class AddressTest extends TestCase
{
    public function testIsResidential(): void
    {
        $yankee_stadium = new Address(['1 E 161 St'], 'The Bronx', 'NY', '10451', 'US');
        $this->assertFalse($yankee_stadium->isResidential());

        $dodger_stadium = new Address(['1000 Elysian Park Ave'], 'Los Angeles', 'CA', '90012', 'US', true);
        $this->assertTrue($dodger_stadium->isResidential());
    }
}
