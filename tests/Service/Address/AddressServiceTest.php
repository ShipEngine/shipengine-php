<?php declare(strict_types=1);

namespace Service\Address;

use PHPUnit\Framework\TestCase;
use ShipEngine\Message\ShipEngineError;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateParams;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\ShipEngine;

/**
 * Tests the method provided in the `AddressService` that allows for single address validation.
 *
 * @covers \ShipEngine\Model\Address\Address
 * @covers \ShipEngine\Service\Address\AddressTrait
 * @covers \ShipEngine\Service\Address\AddressService
 * @covers \ShipEngine\Model\Address\AddressValidateParams
 * @covers \ShipEngine\Model\Address\AddressValidateResult
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
     * @var AddressValidateParams
     */
    private AddressValidateParams $good_address;

    /**
     * @var AddressValidateParams
     */
    private AddressValidateParams $bad_address;

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
        $this->good_address = new AddressValidateParams(
            array('4 Jersey St', 'ste 200'),
            'US',
            'Boston',
            'MA',
            '02215'
        );

        $this->bad_address = new AddressValidateParams(
            array('validate-with-error'),
            'US',
            'Boston',
            'MA',
            '02215'
        );

        $this->shipengine = new ShipEngine('baz');
    }

    public function testValidateMethod()
    {
        $validation = $this->shipengine->addresses->validate($this->good_address);
        $this->assertEquals($this->good_address->city_locality, $validation->address['city_locality']);
    }

    /**
     * Test the return type, should be an instance of the `Address` Type.
     */
    public function testReturnType()
    {
        $validation = $this->shipengine->addresses->validate($this->good_address);
//        print_r($validation->jsonSerialize());
        $this->assertInstanceOf(AddressValidateResult::class, $validation);
    }

    public function testValidateWithError()
    {
        $this->assertInstanceOf(AddressValidateResult::class,
            $this->shipengine->addresses->validate($this->bad_address));
    }

    public function testJsonSerialize()
    {
        $this->assertIsString($this->shipengine->addresses->validate($this->good_address)->jsonSerialize());
    }
}
