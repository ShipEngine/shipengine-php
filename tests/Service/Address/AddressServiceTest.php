<?php declare(strict_types=1);

namespace Service\Address;

use PHPUnit\Framework\TestCase;
use ShipEngine\ShipEngine;

/**
 * Tests the methods provided in the `AddressService`.
 *
 * @covers \ShipEngine\Service\Address\AddressTrait
 * @covers \ShipEngine\Service\Address\AddressService
 * @covers \ShipEngine\Service\AbstractService
 * @covers \ShipEngine\Service\ServiceFactory
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 */
final class AddressServiceTest extends TestCase
{
    /**
     * @var ShipEngine
     */
    private ShipEngine $shipengine;

    /**
     * Import `simengine/rpc/rpc.json` into *Hoverfly* before class instantiation.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        exec('hoverctl import simengine/rpc/rpc.json');
    }

    /**
     * Delete `simengine/rpc/rpc.json` from *Hoverfly*.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/rpc/rpc.json');
    }

    /**
     * Pass an `api-key` into the new instance of the *ShipEngine* class.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->shipengine = new ShipEngine('baz');
    }

    public function testValidateMethod(): void
    {
        $params = array(
            'street' => [
                '4 Jersey St',
                'ste 200'
            ],
            'city' => 'Boston',
            'state' => 'MA',
            'postal_code' => '02215',
            'country_code' => 'US',
        );

        $validation = $this->shipengine->addresses->validate($params);

        $this->assertEquals($params['city'], $validation->city_locality);
    }
}
