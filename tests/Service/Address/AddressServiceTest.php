<?php declare(strict_types=1);

namespace Service\Address;

use DateInterval;
use PHPUnit\Framework\TestCase;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\ShipEngine;

/**
 * Tests the method provided in the `AddressService` that allows for single address validation.
 *
 * @covers \ShipEngine\Util\VersionInfo
 * @covers \ShipEngine\Message\Events\ResponseReceivedEvent
 * @covers \ShipEngine\Message\Events\RequestSentEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEvent
 * @covers \ShipEngine\Util\Assert
 * @covers \ShipEngine\Service\Address\AddressService
 * @covers \ShipEngine\Model\Address\Address
 * @covers \ShipEngine\Model\Address\AddressValidateResult
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\Service\ShipEngineConfig
 * @covers \ShipEngine\Util\ShipEngineSerializer
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\Message\ValidationException
 * @covers \ShipEngine\Message\ShipEngineException
 * @backupStaticAttributes enabled
 * @runTestsInSeparateProcesses
 */
final class AddressServiceTest extends TestCase
{
    /**
     * @var ShipEngine
     */
    private static ShipEngine $shipengine;

    /**
     * @var Address
     */
    private static Address $good_address;

    /**
     * @var Address
     */
    private static Address $valid_residential_address;

    /**
     * @var Address
     */
    private static Address $canada_address;

    /**
     * @var Address
     */
    private static Address $multi_line_address;

    /**
     * @var Address
     */
    private static Address $unknown_address_type;

    /**
     * @var Address
     */
    private static Address $bad_address;

    /**
     * @var Address
     */
    private static Address $validate_with_warning;

    /**
     * @var Address
     */
    private static Address $validate_with_error;

    /**
     * @var Address
     */
    private static Address $non_latin_chars_address;

    /**
     * @var Address
     */
    private static Address $get_rpc_server_error;


    /**
     * Pass an `api-key` into the new instance of the *ShipEngine* class and instantiate fixtures.
     *
     * @beforeClass
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        putenv('CLIENT_BASE_URI=https://simengine.herokuapp.com/jsonrpc');
        self::$non_latin_chars_address = new Address(
            array(
                '上鳥羽角田町６８',
                'validate-with-non-latin-chars'
            ),
            '南区',
            '京都',
            '601-8104',
            'JP'
        );
        self::$good_address = new Address(
            array('4 Jersey St', 'ste 200'),
            'Boston',
            'MA',
            '02215',
            'US',
        );
        self::$unknown_address_type = new Address(
            array('4 Jersey St', 'validate-unknown-address'),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$valid_residential_address = new Address(
            array('4 Jersey St', 'validate-residential-address'),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$multi_line_address = new Address(
            array(
                '4 Jersey St',
                'ste 200',
                'multi-line-address'
            ),
            'Boston',
            'MA',
            '02215',
            'US',
        );
        self::$canada_address = new Address(
            array('170 Princes\' Blvd', 'validate-canadian-address'),
            'Toronto',
            'ON',
            'M6K 3C3',
            'CA',
        );
        self::$bad_address = new Address(
            array(
                '4 Jersey St',
                'with-error'
            ),
            'Boston',
            'MA',
            '02215',
            'US'
        );
        self::$validate_with_warning = new Address(
            array('170 Princes\' Blvd', 'validate-with-warning'),
            'Toronto',
            'ON',
            'M6K 3C3',
            'CA',
        );
        self::$validate_with_error = new Address(
            array('124 Conch St', 'validate-with-error'),
            'Bikini Bottom',
            'Pacific Ocean',
            '4A6 G67',
            'US'
        );
        self::$get_rpc_server_error = new Address(
            array('4 Jersey St', 'ste 200', 'rpc-server-error'),
            'Boston',
            'MA',
            '02215',
            'US',
        );

        self::$shipengine = new ShipEngine(array(
            'api_key' => 'baz',
            'page_size' => 75,
            'retries' => 1,
            'timeout' => new DateInterval('PT15000S')
        ));
    }

    /**
     * Test the return type, should be an instance of the `AddressTest` Type.
     */
    public function testReturnType()
    {
        $validation = self::$shipengine->validateAddress(self::$good_address);

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
        $validation = self::$shipengine->validateAddress(self::$valid_residential_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->normalized_address);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertEquals(
            strtoupper(self::$valid_residential_address->street[0]),
            $validation->normalized_address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$valid_residential_address->city_locality),
            $validation->normalized_address['city_locality']
        );
        $this->assertEquals(
            self::$valid_residential_address->state_province,
            $validation->normalized_address['state_province']
        );
        $this->assertEquals(
            self::$valid_residential_address->postal_code,
            $validation->normalized_address['postal_code']
        );
        $this->assertEquals(
            self::$valid_residential_address->country_code,
            $validation->normalized_address['country_code']
        );
        $this->assertTrue($validation->normalized_address['residential']);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
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
        $validation = self::$shipengine->validateAddress(self::$good_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->normalized_address);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertEquals(
            strtoupper(self::$good_address->street[0]),
            $validation->normalized_address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$good_address->city_locality),
            $validation->normalized_address['city_locality']
        );
        $this->assertEquals(
            self::$good_address->state_province,
            $validation->normalized_address['state_province']
        );
        $this->assertEquals(
            self::$good_address->postal_code,
            $validation->normalized_address['postal_code']
        );
        $this->assertEquals(
            self::$good_address->country_code,
            $validation->normalized_address['country_code']
        );
        $this->assertFalse($validation->normalized_address['residential']);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
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
        $validation = self::$shipengine->validateAddress(self::$unknown_address_type);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->normalized_address);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertEquals(
            strtoupper(self::$unknown_address_type->street[0]),
            $validation->normalized_address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$unknown_address_type->city_locality),
            $validation->normalized_address['city_locality']
        );
        $this->assertEquals(
            self::$unknown_address_type->state_province,
            $validation->normalized_address['state_province']
        );
        $this->assertEquals(
            self::$unknown_address_type->postal_code,
            $validation->normalized_address['postal_code']
        );
        $this->assertEquals(
            self::$unknown_address_type->country_code,
            $validation->normalized_address['country_code']
        );
        $this->assertNull($validation->normalized_address['residential']);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
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
        $validation = self::$shipengine->validateAddress(self::$multi_line_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->normalized_address);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertArrayHasKey(0, $validation->normalized_address['street']);
        $this->assertArrayHasKey(1, $validation->normalized_address['street']);
        $this->assertArrayNotHasKey(3, $validation->normalized_address['street']);
        $this->assertEquals(
            strtoupper(self::$multi_line_address->street[0] . ' ' . self::$multi_line_address->street[1]),
            $validation->normalized_address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$multi_line_address->city_locality),
            $validation->normalized_address['city_locality']
        );
        $this->assertEquals(
            self::$multi_line_address->state_province,
            $validation->normalized_address['state_province']
        );
        $this->assertEquals(
            self::$multi_line_address->postal_code,
            $validation->normalized_address['postal_code']
        );
        $this->assertEquals(
            self::$multi_line_address->country_code,
            $validation->normalized_address['country_code']
        );
        $this->assertFalse($validation->normalized_address['residential']);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests with that the `postal-code` is numeric and matches the postal_code passed in.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * **postal_code** is numeric and matches original postal_code.
     * - **residential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testNumericPostalCode()
    {
        $validation = self::$shipengine->validateAddress(self::$good_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->normalized_address);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertEquals(
            strtoupper(self::$good_address->street[0]),
            $validation->normalized_address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$good_address->city_locality),
            $validation->normalized_address['city_locality']
        );
        $this->assertIsNumeric(self::$good_address->postal_code);
        $this->assertEquals(self::$good_address->postal_code, $validation->normalized_address['postal_code']);
        $this->assertEquals(self::$good_address->country_code, $validation->normalized_address['country_code']);
        $this->assertFalse($validation->normalized_address['residential']);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests with that the `postal-code` is alpha-numeric and matches the postal_code passed in.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **postal_code** is alpha-numeric and matches the original address.
     * - **residential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testAlphaPostalCode()
    {
        $validation = self::$shipengine->validateAddress(self::$canada_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->normalized_address);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertEquals(self::$canada_address->street[0], $validation->normalized_address['street'][0]);
        $this->assertEquals(
            self::$canada_address->city_locality,
            $validation->normalized_address['city_locality']
        );
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', self::$canada_address->postal_code);
        $this->assertEquals(self::$canada_address->postal_code, $validation->normalized_address['postal_code']);
        $this->assertEquals(self::$canada_address->country_code, $validation->normalized_address['country_code']);
        $this->assertFalse($validation->normalized_address['residential']);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests as address with non-latin characters and confirms the normalization.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **normalized address** has proper normalization applied to non-latin characters.
     * - **residential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testAddressWithNonLatinCharacters()
    {
        $validation = self::$shipengine->validateAddress(self::$non_latin_chars_address);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->normalized_address);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertEquals('68 Kamitobatsunodacho', $validation->normalized_address['street'][0]);
        $this->assertEquals(
            'Kyoto-Shi Minami-Ku',
            $validation->normalized_address['city_locality']
        );
        $this->assertEquals('Kyoto', $validation->normalized_address['state_province']);
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9-]*$/',
            self::$non_latin_chars_address->postal_code
        );
        $this->assertEquals(
            self::$non_latin_chars_address->postal_code,
            $validation->normalized_address['postal_code']
        );
        $this->assertEquals(
            self::$non_latin_chars_address->country_code,
            $validation->normalized_address['country_code']
        );
        $this->assertFalse($validation->normalized_address['residential']);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests a validation with `error` messages.
     *
     * `Assertions:`
     * - **valid** flag is `false`.
     * - **address** is null.
     * - That **error** messages are provided.
     * - There are no **warning** messages.
     */
    public function testValidateWithError()
    {
        $validation = self::$shipengine->validateAddress(self::$validate_with_error);

        $this->assertFalse($validation->valid);
        $this->assertNull($validation->normalized_address);
        $this->assertNotEmpty($validation->errors);
        $this->assertIsArray($validation->errors);
        $this->assertIsString($validation->errors[0]);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests a validation with `warning` messages.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **residential** flag on the normalized address is `false`.
     * - That **warning** messages are provided.
     * - There are no **error** messages.
     */
    public function testValidateWithWarning()
    {
        $validation = self::$shipengine->validateAddress(self::$validate_with_warning);

        $this->assertTrue($validation->valid);
        $this->assertIsArray($validation->normalized_address);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertEquals(self::$validate_with_warning->street[0], $validation->normalized_address['street'][0]);
        $this->assertEquals(
            self::$validate_with_warning->city_locality,
            $validation->normalized_address['city_locality']
        );
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', self::$validate_with_warning->postal_code);
        $this->assertEquals(
            self::$validate_with_warning->postal_code,
            $validation->normalized_address['postal_code']
        );
        $this->assertEquals(
            self::$validate_with_warning->country_code,
            $validation->normalized_address['country_code']
        );
        $this->assertFalse($validation->normalized_address['residential']);
        $this->assertEmpty($validation->errors);
        $this->assertNotEmpty($validation->warnings);
        $this->assertIsString($validation->warnings[0]);
        $this->assertEquals(
            <<<'EOT'
This address has been verified down to the house/building level (highest possible accuracy with the provided data)
EOT
            ,
            $validation->warnings[0]
        );
    }

    /**
     * Tests a validation with `error` messages.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is "Invalid address. At least one address line is required.".
     */
    public function testNoAddressLinesValidationError()
    {
        try {
            $validationError = new Address(
                array(),
                'Boston',
                'MA',
                '02215',
                'US',
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['error_code']);
            $this->assertEquals(
                'Invalid address. At least one address line is required.',
                $error['message']
            );
        }
    }

    /**
     * Tests a validation with too many address lines.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is "Invalid address. No more than 3 street lines are allowed.".
     */
    public function testTooManyAddressLinesValidationError()
    {
        try {
            new Address(
                array('4 Jersey St', 'Ste 200', '2nd Floor', 'Clubhouse Level'),
                'Boston',
                'MA',
                '02215',
                'US',
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['error_code']);
            $this->assertEquals(
                'Invalid address. No more than 3 street lines are allowed.',
                $error['message']
            );
        }
    }

    /**
     * Tests a validation with missing `city`.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testMissingCity()
    {
        try {
            $validationError = new Address(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                '',
                'MA',
                '02215',
                'US',
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['error_code']);
            $this->assertEquals(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests a validation with missing `state`.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testMissingStatePostalAndCity()
    {
        try {
            $validationError = new Address(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                '',
                '',
                '',
                'US',
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['error_code']);
            $this->assertEquals(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests a validation with missing `postal_code`.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testMissingPostalCode()
    {
        try {
            new Address(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                'Boston',
                'MA',
                '',
                'US',
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['error_code']);
            $this->assertEquals(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests a validation with missing `country_code`.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. The country must be specified.".
     */
    public function testMissingCountryCode()
    {
        try {
            $validationError = new Address(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                'Boston',
                'MA',
                '02215',
                '',
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['error_code']);
            $this->assertEquals(
                'Invalid address. The country must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests a validation with invalid `country_code`.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. XX is not a valid country code."
     * (where XX is the value that was specified).
     */
    public function testInvalidCountryCode()
    {
        try {
            $validationError = new Address(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                'Boston',
                'MA',
                '02215',
                'USA',
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['error_code']);
            $this->assertEquals(
                "Invalid address. USA is not a valid country code.",
                $error['message']
            );
        }
    }

    public function testServerSideError()
    {
        try {
            self::$shipengine->validateAddress(self::$get_rpc_server_error);
        } catch (SystemException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(SystemException::class, $e);
            $this->assertNotEmpty($error['request_id']);
            $this->assertStringStartsWith('req_', $error['request_id']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('system', $error['type']);
            $this->assertEquals('unspecified', $error['error_code']);
            $this->assertEquals(
                "Unable to connect to the database",
                $error['message']
            );
        }
    }

    public function testNoNameCompanyPhone()
    {
        $validation = self::$shipengine->validateAddress(self::$good_address);

        $this->assertTrue($validation->valid);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertEquals(
            strtoupper(self::$good_address->street[0]),
            $validation->normalized_address['street'][0]
        );
        $this->assertEquals(
            strtoupper(self::$good_address->city_locality),
            $validation->normalized_address['city_locality']
        );
        $this->assertEquals(self::$good_address->state_province, $validation->normalized_address['state_province']);
        $this->assertEquals(self::$good_address->postal_code, $validation->normalized_address['postal_code']);
        $this->assertEquals(self::$good_address->country_code, $validation->normalized_address['country_code']);
//        $this->assertFalse(
//array_key_exists($validation->normalized_address['name'],
// $validation->normalized_address)
//);
//        $this->assertFalse(
//array_key_exists($validation->normalized_address['phone'],
// $validation->normalized_address)
//);
//        $this->assertFalse(
//array_key_exists($validation->normalized_address['company'],
// $validation->normalized_address)
//);
    }

    public function testWithNameCompanyPhone()
    {
        $address = new Address(
            array(
                '4 Jersey St',
                'ste 200',
                'validate-with-personal-info'
            ),
            'Boston',
            'MA',
            '02215',
            'US',
            false,
            'Bruce Wayne',
            '1234567891',
            'ShipEngine'
        );
        $validation = self::$shipengine->validateAddress($address);

        $this->assertTrue($validation->valid);
        $this->assertNotEmpty($validation->normalized_address);
        $this->assertEquals(
            strtoupper($address->name),
            $validation->normalized_address['name']
        );
        $this->assertEquals($address->phone, $validation->normalized_address['phone']);
        $this->assertEquals(
            strtoupper($address->company),
            $validation->normalized_address['company_name']
        );
        $this->assertEmpty($validation->warnings);
        $this->assertEmpty($validation->errors);
    }

    // Normalize Address Tests
    public function testNormalizeAddressReturnType()
    {
        $this->assertInstanceOf(Address::class, self::$shipengine->normalizeAddress(self::$good_address));
    }

    /**
     * Tests the `normalizeAddress` method with a valid residential address.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **address** is returned and matches the given address.
     * - **residential** flag on the normalized address is set to `true`.
     */
    public function testNormalizeValidResidentialAddress()
    {
        $validation = self::$shipengine->normalizeAddress(self::$valid_residential_address);

        $this->assertNotNull($validation);
        $this->assertTrue($validation->residential);
        $this->assertEquals(
            strtoupper(self::$valid_residential_address->street[0]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper(self::$valid_residential_address->city_locality),
            $validation->city_locality
        );
        $this->assertEquals(
            self::$valid_residential_address->state_province,
            $validation->state_province
        );
        $this->assertEquals(
            self::$valid_residential_address->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            self::$valid_residential_address->country_code,
            $validation->country_code
        );
    }

    /**
     * Tests the `normalizeAddress` method with a valid commercial address.
     *
     * `Assertions:`
     * - **valid** flag is `false`.
     * - **address** is returned and matches the given address.
     * - **residential** flag on the normalized address is set to `true`.
     */
    public function testNormalizeValidCommercialAddress()
    {
        $validation = self::$shipengine->normalizeAddress(self::$good_address);

        $this->assertNotNull($validation);
        $this->assertFalse($validation->residential);
        $this->assertEquals(
            strtoupper(self::$good_address->street[0]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper(self::$good_address->city_locality),
            $validation->city_locality
        );
        $this->assertEquals(
            self::$good_address->state_province,
            $validation->state_province
        );
        $this->assertEquals(
            self::$good_address->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            self::$good_address->country_code,
            $validation->country_code
        );
    }

    /**
     * Tests the `normalizeAddress` method with a valid address of unknown type (e.g. residential vs commercial).
     *
     * `Assertions:`
     * - **valid** flag is `false`.
     * - **address** is returned and matches the given address.
     * - **residential** flag on the normalized address is set to `null`.
     */
    public function testNormalizeValidAddressUnknownType()
    {
        $validation = self::$shipengine->normalizeAddress(self::$unknown_address_type);

        $this->assertNotNull($validation);
        $this->assertNull($validation->residential);
        $this->assertEquals(
            strtoupper(self::$unknown_address_type->street[0]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper(self::$unknown_address_type->city_locality),
            $validation->city_locality
        );
        $this->assertEquals(
            self::$unknown_address_type->state_province,
            $validation->state_province
        );
        $this->assertEquals(
            self::$unknown_address_type->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            self::$unknown_address_type->country_code,
            $validation->country_code
        );
    }

    /**
     * Tests `normalizeAddress()` with an `multi-line address` or an address that has values for `address_line1`,
     * `address_line2`, and `address_line3` in the parameters of the request.
     *
     * `Assertions:`
     * - Array keys passed in are the keys we get back include the necessary normalization
     * provided by ShipEngine API.
     * - **address** is returned and matches the given address.
     * - **residential** flag on the normalized address is set to `false`.
     */
    public function testNormalizeAddressWithMultiLineAddress()
    {
        $validation = self::$shipengine->normalizeAddress(self::$multi_line_address);

        $this->assertArrayHasKey(0, $validation->street);
        $this->assertArrayHasKey(1, $validation->street);
        $this->assertArrayNotHasKey(3, $validation->street);
        $this->assertEquals(
            strtoupper(self::$multi_line_address->street[0] . ' ' . self::$multi_line_address->street[1]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper(self::$multi_line_address->city_locality),
            $validation->city_locality
        );
        $this->assertEquals(
            self::$multi_line_address->state_province,
            $validation->state_province
        );
        $this->assertEquals(
            self::$multi_line_address->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            self::$multi_line_address->country_code,
            $validation->country_code
        );
        $this->assertFalse($validation->residential);
    }

    public function testJsonSerialize()
    {
        $this->assertIsArray(self::$shipengine->validateAddress(self::$good_address, null)->jsonSerialize());
    }
}
