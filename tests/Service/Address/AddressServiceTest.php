<?php declare(strict_types=1);

namespace Service\Address;

use PHPUnit\Framework\TestCase;
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
    private static ShipEngine $shipengine;

    /**
     * @var AddressValidateParams
     */
    private static AddressValidateParams $good_address;

    /**
     * @var AddressValidateParams
     */
    private static AddressValidateParams $valid_residential_address;

    /**
     * @var AddressValidateParams
     */
    private static AddressValidateParams $bad_address;

    /**
     * Pass an `api-key` into the new instance of the *ShipEngine* class and instantiate fixtures.
     *
     * @return void
     */
    protected function setUp(): void
    {
        self::$good_address = new AddressValidateParams(
            array('4 Jersey St', 'ste 200'),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$valid_residential_address = new AddressValidateParams(
            array('validate-residential'),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$bad_address = new AddressValidateParams(
            array('with-error'),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$shipengine = new ShipEngine('baz');
    }

    public function testValidateMethod()
    {
        $validation = self::$shipengine->addresses->validate(self::$good_address);
        $this->assertEquals(self::$good_address->city_locality, $validation->address['city_locality']);
    }

    /**
     * Test the return type, should be an instance of the `Address` Type.
     */
    public function testReturnType()
    {
        $validation = self::$shipengine->addresses->validate(self::$good_address);

        $this->assertInstanceOf(AddressValidateResult::class, $validation);
    }

    public function testValidResidentialAddress()
    {
        $validation = self::$shipengine->addresses->validate(self::$valid_residential_address);


        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->address);
        $this->assertNotEmpty($validation->address);
//        $this->assertEquals(self::$valid_residential_address, $validation->address);
        $this->assertTrue($validation->address['residential']);
        $this->assertEmpty($validation->address['errors']);
        $this->assertEmpty($validation->address['warnings']);
    }

    public function testValidateWithError()
    {
        $this->assertInstanceOf(
            AddressValidateResult::class,
            self::$shipengine->addresses->validate(self::$bad_address)
        );
    }

    public function testJsonSerialize()
    {
        $this->assertIsString(self::$shipengine->addresses->validate(self::$good_address)->jsonSerialize());
    }
}
