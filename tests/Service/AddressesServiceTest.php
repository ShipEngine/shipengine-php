<?php declare(strict_types=1);

namespace ShipEngine\Service\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngine;
use ShipEngine\Models\AddressQuery;

final class AddressesServiceTest extends TestCase
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

    public function testAddressQuery(): void
    {
        // AddressQuery

        // Arguments
    }
    
    public function testAddressValidate(): void
    {
        // AddressQuery

        // Arguments
    }

    public function testAddressNormalize(): void
    {
        // AddressQuery

        // Arguments
    }
}
