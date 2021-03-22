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
    private static AddressValidateParams $canada_address;

    /**
     * @var AddressValidateParams
     */
    private static AddressValidateParams $multi_line_address;

    /**
     * @var AddressValidateParams
     */
    private static AddressValidateParams $unknown_address_type;

    /**
     * @var AddressValidateParams
     */
    private static AddressValidateParams $bad_address;

    /**
     * @var AddressValidateParams
     */
    private static AddressValidateParams $non_latin_chars_address;

    /**
     * Pass an `api-key` into the new instance of the *ShipEngine* class and instantiate fixtures.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
//        putenv('CLIENT_BASE_URI=https://simengine.herokuapp.com/');

        self::$good_address = new AddressValidateParams(
            array('4 Jersey St', 'ste 200'),
            'Boston',
            'MA',
            '02215',
            'US',
        );
        self::$unknown_address_type = new AddressValidateParams(
            array('4 Jersey St', 'validate-unknown-address'),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$valid_residential_address = new AddressValidateParams(
            array('4 Jersey St', 'validate-residential-address'),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$multi_line_address = new AddressValidateParams(
            array(
                '4 Jersey St',
                'ste 200',
                '2nd Floor',
                'multi-line-address'
            ),
            'Boston',
            'MA',
            '02215',
            'US',
        );
        self::$canada_address = new AddressValidateParams(
            array('170 Princes\' Blvd'),
            'Toronto',
            'ON',
            'M6K 3C3',
            'CA',
        );
        self::$bad_address = new AddressValidateParams(
            array(
                '4 Jersey St',
                'with-error'
            ),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$shipengine = new ShipEngine('baz');
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
     *
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
        $this->assertEquals(
            strtoupper(self::$valid_residential_address->street[0]),
            $validation->address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$valid_residential_address->city_locality),
            $validation->address['city_locality']
        );
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
        $this->assertEquals(
            strtoupper(self::$good_address->street[0]),
            $validation->address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$good_address->city_locality),
            $validation->address['city_locality']
        );
        $this->assertEquals(self::$good_address->state_province, $validation->address['state_province']);
        $this->assertEquals(self::$good_address->postal_code, $validation->address['postal_code']);
        $this->assertEquals(self::$good_address->country_code, $validation->address['country_code']);
        $this->assertFalse($validation->address['residential']);
        $this->assertEmpty($validation->address['errors']);
        $this->assertEmpty($validation->address['warnings']);
    }

    /**
     * Tests with an address of unknown type (residential|commercial).
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
        $this->assertEquals(
            strtoupper(self::$unknown_address_type->street[0]),
            $validation->address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$unknown_address_type->city_locality),
            $validation->address['city_locality']
        );
        $this->assertEquals(self::$unknown_address_type->state_province, $validation->address['state_province']);
        $this->assertEquals(self::$unknown_address_type->postal_code, $validation->address['postal_code']);
        $this->assertEquals(self::$unknown_address_type->country_code, $validation->address['country_code']);
        $this->assertNull($validation->address['residential']);
        $this->assertEmpty($validation->address['errors']);
        $this->assertEmpty($validation->address['warnings']);
    }

    /**
     * Tests with an `multi-line address` or an address that has values for `address_line1`,
     * `address_line2`, and `address_line3` in the parameters of the request.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **residential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testMultiLineAddress()
    {
        $validation = self::$shipengine->addresses->validate(self::$multi_line_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->address);
        $this->assertNotEmpty($validation->address);
        $this->assertArrayHasKey(0, $validation->address['street']);
        $this->assertArrayHasKey(1, $validation->address['street']);
        $this->assertEquals(
            strtoupper(self::$multi_line_address->street[0] . ' ' . self::$multi_line_address->street[1]),
            $validation->address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$multi_line_address->street[2]),
            $validation->address['street'][1]
        );
        $this->assertEquals(
            strtoupper(self::$multi_line_address->city_locality),
            $validation->address['city_locality']
        );
        $this->assertEquals(self::$multi_line_address->state_province, $validation->address['state_province']);
        $this->assertEquals(self::$multi_line_address->postal_code, $validation->address['postal_code']);
        $this->assertEquals(self::$multi_line_address->country_code, $validation->address['country_code']);
        $this->assertFalse($validation->address['residential']);
        $this->assertEmpty($validation->address['errors']);
        $this->assertEmpty($validation->address['warnings']);
    }

    /**
     * Tests with that the `postal-code` is numeric and matches the postal_code passed in.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **residential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testNumericPostalCode()
    {
        $validation = self::$shipengine->addresses->validate(self::$good_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->address);
        $this->assertNotEmpty($validation->address);
        $this->assertEquals(
            strtoupper(self::$good_address->street[0]),
            $validation->address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$good_address->city_locality),
            $validation->address['city_locality']
        );
        $this->assertIsNumeric(self::$good_address->postal_code);
        $this->assertEquals(self::$good_address->postal_code, $validation->address['postal_code']);
        $this->assertEquals(self::$good_address->country_code, $validation->address['country_code']);
        $this->assertFalse($validation->address['residential']);
        $this->assertEmpty($validation->address['errors']);
        $this->assertEmpty($validation->address['warnings']);
    }

    public function testAlphaPostalCode()
    {
        $validation = self::$shipengine->addresses->validate(self::$canada_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->address);
        $this->assertNotEmpty($validation->address);
        $this->assertEquals(self::$canada_address->street, $validation->address['street']);
        $this->assertEquals(
            self::$canada_address->city_locality,
            $validation->address['city_locality']
        );
        $this->assertMatchesRegularExpression('/[[:alnum:]\s]/', self::$canada_address->postal_code);
        $this->assertEquals(self::$canada_address->postal_code, $validation->address['postal_code']);
        $this->assertEquals(self::$canada_address->country_code, $validation->address['country_code']);
        $this->assertFalse($validation->address['residential']);
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
