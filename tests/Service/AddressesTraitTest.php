<?php declare(strict_types=1);

namespace ShipEngine\Service\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\Exception\ErrorException;
use ShipEngine\Model\Address;
use ShipEngine\Model\AddressQuery;
use ShipEngine\Model\AddressQueryResult;
use ShipEngine\ShipEngine;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\Model\Address
 * @covers \ShipEngine\Model\AddressQuery
 * @covers \ShipEngine\Model\AddressQueryResult
 * @covers \ShipEngine\Service\AbstractService
 * @covers \ShipEngine\Service\AddressesService
 * @covers \ShipEngine\Service\AddressesTrait
 * @covers \ShipEngine\Service\ServiceFactory
 */
final class AddressesTraitTest extends TestCase
{
    private ShipEngine $shipengine;
    
    public static function setUpBeforeClass(): void
    {
        exec('hoverctl import simengine/v1/addresses.json');
    }
        
    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/v1/addresses.json');
    }
    
    protected function setUp(): void
    {
        $this->shipengine = new ShipEngine(['api_key' => 'foobar', 'base_uri' => 'http://localhost:8500/v1']);
    }

    public function testQueryAddressArgs(): void
    {
        $result = $this->shipengine->queryAddress(['1 E 161 St'], 'The Bronx', 'NY', '10451', 'US');

        $this->assertEmpty($result->exceptions);
    }
    
    public function testQueryAddressQuery(): void
    {
        $yankee_stadium = new AddressQuery(['1 E 161 St'], 'The Bronx', 'NY', '10451', 'US');
        $result = $this->shipengine->queryAddress($yankee_stadium);

        $this->assertEmpty($result->exceptions);
    }

    public function testValidateAddressArgs(): void
    {
        $valid = $this->shipengine->validateAddress(['1000 Elysion Ave'], 'Los Angeles', 'CA', '90012', 'US');

        $this->assertFalse($valid);
    }
    
    public function testValidateAddressQuery(): void
    {
        $dodger_stadium = new AddressQuery(['1000 Elysion Ave'], 'Los Angeles', 'CA', '90012', 'US');
        $valid = $this->shipengine->validateAddress($dodger_stadium);

        $this->assertFalse($valid);
    }

    public function testNormalizeAddressArgs(): void
    {
        $normalized = $this->shipengine->normalizeAddress(['501 Crawford St, Houston'], null, null, null, 'US');

        $this->assertEquals($normalized->state_province, 'TX');
    }
    
    public function testNormalizeAddress(): void
    {
        $yankee_stadium = new AddressQuery(['1 E 161 St'], 'The Bronx', 'NY', '10451', 'US');
        $normalized = $this->shipengine->normalizeAddress($yankee_stadium);
        
        $this->assertEquals($yankee_stadium->state_province, $normalized->state_province);
        
        $this->expectException(ErrorException::class);
        
        $wrigley_field = new AddressQuery(['1060 W Addison St'], 'Chicago', 'IL', '60613');
        $normalized = $this->shipengine->normalizeAddress($wrigley_field);
    }
}
