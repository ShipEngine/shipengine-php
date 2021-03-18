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
    private static AddressValidateParams $unknown_address_type;

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
            'US',
            false
        );
        self::$unknown_address_type = new AddressValidateParams(
            array('validate-unknown-address'),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$valid_residential_address = new AddressValidateParams(
            array('validate-residential-address'),
            'Boston',
            'MA',
            '02215',
            'US',
            true
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

    /**
     * Tests with a valid residential address.
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **residential** flag on the normalized address is set to `true`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testValidResidentialAddress()
    {
        $validation = self::$shipengine->addresses->validate(self::$valid_residential_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->address);
        $this->assertNotEmpty($validation->address);
//        TODO: Add in line 100 when hoverfly is removed from testing workflow and moved to hosted solution.
//        $this->assertEquals(self::$valid_residential_address->street, $validation->address['street']);
        $this->assertEquals(self::$valid_residential_address->city_locality, $validation->address['city_locality']);
        $this->assertEquals(self::$valid_residential_address->state_province, $validation->address['state_province']);
        $this->assertEquals(self::$valid_residential_address->postal_code, $validation->address['postal_code']);
        $this->assertEquals(self::$valid_residential_address->country_code, $validation->address['country_code']);
        $this->assertTrue($validation->address['residential']);
        $this->assertEmpty($validation->address['errors']);
        $this->assertEmpty($validation->address['warnings']);
    }

    /**
     * Tests with a valid commercial address.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **residential** flag on the normalized address is set to `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testValidCommercialAddress()
    {
        $validation = self::$shipengine->addresses->validate(self::$good_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->address);
        $this->assertNotEmpty($validation->address);
//        TODO: Add in line 135 when hoverfly is removed from testing workflow and moved to hosted solution.
//        $this->assertEquals(self::$good_address->street, $validation->address['street']);
        $this->assertEquals(self::$good_address->city_locality, $validation->address['city_locality']);
        $this->assertEquals(self::$good_address->state_province, $validation->address['state_province']);
        $this->assertEquals(self::$good_address->postal_code, $validation->address['postal_code']);
        $this->assertEquals(self::$good_address->country_code, $validation->address['country_code']);
        $this->assertFalse($validation->address['residential']);
        $this->assertEmpty($validation->address['errors']);
        $this->assertEmpty($validation->address['warnings']);
    }

    /**
     * Tests with an address of unknown type (residential|commercial) .
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **residential** flag on the normalized address is `unknown`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testAddressUnknownType()
    {
        $validation = self::$shipengine->addresses->validate(self::$unknown_address_type);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->address);
        $this->assertNotEmpty($validation->address);
//        TODO: Add in line 166 when hoverfly is removed from testing workflow and moved to hosted solution.
//        $this->assertEquals(self::self::$$unknown_address_type->street, $validation->address['street']);
        $this->assertEquals(self::$unknown_address_type->city_locality, $validation->address['city_locality']);
        $this->assertEquals(self::$unknown_address_type->state_province, $validation->address['state_province']);
        $this->assertEquals(self::$unknown_address_type->postal_code, $validation->address['postal_code']);
        $this->assertEquals(self::$unknown_address_type->country_code, $validation->address['country_code']);
        $this->assertNull($validation->address['residential']);
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
