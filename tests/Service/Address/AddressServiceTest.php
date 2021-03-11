<?php declare(strict_types=1);

namespace Service\Address;

use PHPUnit\Framework\TestCase;
use ShipEngine\Message\ShipEngineError;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateParams;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\ShipEngine;

/**
 * Tests the methods provided in the `AddressService`.
 *
 * @covers \ShipEngine\Model\Address\Address
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
     * @var AddressValidateParams
     */
    private AddressValidateParams $goodAddress;

    /**
     * @var AddressValidateParams
     */
    private AddressValidateParams $badAddress;

    /**
     * Import `simengine/rpc/rpc.json` into *Hoverfly* before class instantiation.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
    }

    /**
     * Delete `simengine/rpc/rpc.json` from *Hoverfly*.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
    }

    /**
     * Pass an `api-key` into the new instance of the *ShipEngine* class.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->goodAddress = new AddressValidateParams(
            array('4 Jersey St', 'ste 200'),
            'US',
            'Boston',
            'MA',
            '02215'
        );

        $this->badAddress = new AddressValidateParams(
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
        $validation = $this->shipengine->addresses->validate($this->goodAddress);
        $this->assertEquals($this->goodAddress->city_locality, $validation->address->city_locality);
    }

    /**
     * Test the return type, should be an instance of the `Address` Type.
     */
    public function testReturnType()
    {
        $validation = $this->shipengine->addresses->validate($this->goodAddress);
        print_r(json_encode($validation, JSON_PRETTY_PRINT));
//        $this->assertInstanceOf(AddressValidateResult::class, $validation);
    }

    public function testValidateWithError()
    {
        $this->assertInstanceOf(AddressValidateResult::class,
            $this->shipengine->addresses->validate($this->badAddress));
    }

//    public function testJsonSerialize()
//    {
//        print_r($this->shipengine->addresses->validate($this->badAddress)->jsonSerialize());
////        print_r($this->shipengine->addresses->validate($this->badAddress)->errors());
//    }
}
