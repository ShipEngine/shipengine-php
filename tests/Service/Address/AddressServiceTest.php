<?php

declare(strict_types=1);

namespace Service\Address;

use DateInterval;
use PHPUnit\Framework\TestCase;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\ShipEngine;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorSource;
use ShipEngine\Util\Constants\ErrorType;

/**
 * Tests the method provided in the `AddressService` that allows for single address validation.
 *
 * @covers \ShipEngine\Util\Assert
 * @covers \ShipEngine\Service\Address\AddressService
 * @covers \ShipEngine\Model\Address\Address
 * @covers \ShipEngine\Model\Address\AddressValidateResult
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineConfig
 * @covers \ShipEngine\Util\ShipEngineSerializer
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\Message\ValidationException
 * @covers \ShipEngine\Message\ShipEngineException
 * @uses   \ShipEngine\Message\Events\ShipEngineEvent
 * @uses   \ShipEngine\Message\Events\ShipEngineEventListener
 * @uses   \ShipEngine\Message\Events\EventMessage
 * @uses   \ShipEngine\Message\Events\EventOptions
 * @uses   \ShipEngine\Util\VersionInfo
 * @uses   \ShipEngine\Message\Events\ResponseReceivedEvent
 * @uses   \ShipEngine\Message\Events\RequestSentEvent
 * @uses   \ShipEngine\Message\Events\ShipEngineEvent
 */
final class AddressServiceTest extends TestCase
{
    /**
     * @var ShipEngine
     */
    private static ShipEngine $shipengine;

    private static Address $valid_address;

    /**
     * Pass an `api-key` into the new instance of the *ShipEngine* class and instantiate fixtures.
     *
     * @beforeClass
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$shipengine = new ShipEngine(
            array(
                'api_key' => 'baz',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        self::$valid_address = new Address(
            array(
                'name' => 'ShipEngine',
                'company' => 'Auctane',
                'phone' => '1-234-567-8910',
                'address_line1' => '3800 N Lamar Blvd.',
                'address_line2' => 'ste 220',
                'city_locality' => 'Austin',
                'state_province' => 'TX',
                'postal_code' => '78756',
                'country_code' => 'US',
                'address_residential_indicator' => 'no'
            )
        );
    }

    /**
     * Test the return type, should be an instance of the `AddressTest` Type.
     */
    public function testReturnType(): void
    {
        $goodAddress = new Address(
            array(
                'address_line1' => '4 Jersey St',
                'address_line2' => 'ste 200',
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddresses($goodAddress);
        $this->assertInstanceOf(AddressValidateResult::class, $validation);
    }

    /**
     * Tests with a valid isResidential address.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **isResidential** flag on the normalized address is set to `true`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testValidResidentialAddress(): void
    {
        $valid_residential_address = new Address(
            array(
                'street' => array('4 Jersey St', 'Apt. 2b'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
                'isResidential' => null
            )
        );
        $validation = self::$shipengine->validateAddresses($valid_residential_address);

        $this->assertTrue($validation->status);
        $this->assertInstanceOf(Address::class, $validation);
        $this->assertNotEmpty($validation);
        $this->assertEquals(
            strtoupper(
                str_replace(
                    '.',
                    '',
                    $valid_residential_address->street[0] . " " . $valid_residential_address->street[1]
                )
            ),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($valid_residential_address->city_locality),
            $validation->city_locality
        );
        $this->assertEquals(
            $valid_residential_address->state_province,
            $validation->state_province
        );
        $this->assertEquals(
            $valid_residential_address->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            $valid_residential_address->country_code,
            $validation->country_code
        );
        $this->assertTrue($validation->isResidential);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests with a valid commercial address.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **isResidential** flag on the normalized address is set to `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testValidCommercialAddress(): void
    {
        $goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddresses($goodAddress);

        $this->assertTrue($validation->status);
        $this->assertInstanceOf(Address::class, $validation);
        $this->assertNotEmpty($validation);
        $this->assertEquals(
            strtoupper($goodAddress->street[0] . " " . $goodAddress->street[1]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($goodAddress->city_locality),
            $validation->city_locality
        );
        $this->assertEquals(
            $goodAddress->state_province,
            $validation->state_province
        );
        $this->assertEquals(
            $goodAddress->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            $goodAddress->country_code,
            $validation->country_code
        );
        $this->assertFalse($validation->isResidential);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests with an address of unknown type (isResidential|commercial).
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **isResidential** flag on the normalized address is `unknown`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testAddressUnknownType(): void
    {
        $unknown_address_type = new Address(
            array(
                'street' => array('4 Jersey St', 'validate-unknown-address'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US'
            )
        );
        $validation = self::$shipengine->validateAddresses($unknown_address_type);

        $this->assertTrue($validation->status);
        $this->assertInstanceOf(Address::class, $validation);
        $this->assertNotEmpty($validation);
        $this->assertEquals(
            strtoupper($unknown_address_type->street[0]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($unknown_address_type->city_locality),
            $validation->city_locality
        );
        $this->assertEquals(
            $unknown_address_type->state_province,
            $validation->state_province
        );
        $this->assertEquals(
            $unknown_address_type->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            $unknown_address_type->country_code,
            $validation->country_code
        );
        $this->assertNull($validation->isResidential);
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
     * - **isResidential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testMultiLineAddress(): void
    {
        $multi_line_address = new Address(
            array(
                'street' => array(
                    '4 Jersey St',
                    'ste 200',
                    '1st Floor'
                ),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddresses($multi_line_address);

        $this->assertTrue($validation->status);
        $this->assertInstanceOf(Address::class, $validation);
        $this->assertNotEmpty($validation);
        $this->assertArrayHasKey(0, $validation->street);
        $this->assertArrayHasKey(1, $validation->street);
        $this->assertEquals(
            strtoupper($multi_line_address->street[0] . ' ' . $multi_line_address->street[1]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($multi_line_address->city_locality),
            $validation->city_locality
        );
        $this->assertEquals(
            $multi_line_address->state_province,
            $validation->state_province
        );
        $this->assertEquals(
            $multi_line_address->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            $multi_line_address->country_code,
            $validation->country_code
        );
        $this->assertFalse($validation->isResidential);
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
     * - **isResidential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testNumericPostalCode(): void
    {
        $goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddresses($goodAddress);

        $this->assertTrue($validation->status);
        $this->assertInstanceOf(Address::class, $validation);
        $this->assertNotEmpty($validation);
        $this->assertEquals(
            strtoupper($goodAddress->street[0] . " " . $goodAddress->street[1]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($goodAddress->city_locality),
            $validation->city_locality
        );
        $this->assertIsNumeric($goodAddress->postal_code);
        $this->assertEquals($goodAddress->postal_code, $validation->postal_code);
        $this->assertEquals($goodAddress->country_code, $validation->country_code);
        $this->assertFalse($validation->isResidential);
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
     * - **isResidential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testAlphaPostalCode(): void
    {
        $canadaAddress = new Address(
            array(
                'street' => array('170 Princes Blvd', 'Ste 200'),
                'city_locality' => 'Toronto',
                'state_province' => 'ON',
                'postal_code' => 'M6K 3C3',
                'country_code' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddresses($canadaAddress);

        $this->assertTrue($validation->status);
        $this->assertInstanceOf(Address::class, $validation);
        $this->assertNotEmpty($validation);
        $this->assertEquals(
            $canadaAddress->street[0] . " " . $canadaAddress->street[1],
            $validation->street[0]
        );
        $this->assertEquals(
            $canadaAddress->city_locality,
            $validation->city_locality
        );
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $canadaAddress->postal_code);
        $this->assertEquals('M6 K 3 C3', $validation->postal_code);
        $this->assertEquals($canadaAddress->country_code, $validation->country_code);
        $this->assertFalse($validation->isResidential);
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
     * - **isResidential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testAddressWithNonLatinCharacters(): void
    {
        $non_latin_chars_address = new Address(
            array(
                'street' => array(
                    '上鳥羽角田町６８'
                ),
                'city_locality' => '南区',
                'state_province' => '京都',
                'postal_code' => '601-8104',
                'country_code' => 'JP'
            )
        );
        $validation = self::$shipengine->validateAddresses($non_latin_chars_address);

        $this->assertTrue($validation->status);
        $this->assertInstanceOf(Address::class, $validation);
        $this->assertNotEmpty($validation);
        $this->assertEquals('68 Kamitobatsunodacho', $validation->street[0]);
        $this->assertEquals(
            'Kyoto-Shi Minami-Ku',
            $validation->city_locality
        );
        $this->assertEquals('Kyoto', $validation->state_province);
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9-]*$/',
            $non_latin_chars_address->postal_code
        );
        $this->assertEquals(
            $non_latin_chars_address->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            $non_latin_chars_address->country_code,
            $validation->country_code
        );
        $this->assertFalse($validation->isResidential);
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
    public function testValidateWithError(): void
    {
        $validate_with_error = new Address(
            array(
                'street' => array('170 Error Blvd'),
                'city_locality' => 'Toronto',
                'state_province' => 'ON',
                'postal_code' => 'M6K 3C3',
                'country_code' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddresses($validate_with_error);

        $this->assertFalse($validation->status);
        $this->assertNull($validation);
        $this->assertNotEmpty($validation->errors);
        $this->assertIsArray($validation->errors);
        $this->assertIsString($validation->errors[0]['message']);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests a validation with `warning` messages.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **isResidential** flag on the normalized address is `false`.
     * - That **warning** messages are provided.
     * - There are no **error** messages.
     */
    public function testValidateWithWarning(): void
    {
        $validate_with_warning = new Address(
            array(
                'street' => array('170 Warning Blvd', 'Apartment 32-B'),
                'city_locality' => 'Toronto',
                'state_province' => 'ON',
                'postal_code' => 'M6K 3C3',
                'country_code' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddresses($validate_with_warning);

        $this->assertTrue($validation->status);
        $this->assertInstanceOf(Address::class, $validation);
        $this->assertNotEmpty($validation);
        $this->assertEquals(
            $validate_with_warning->street[0] . " " . str_replace(
                'Apartment',
                'Apt',
                $validate_with_warning->street[1]
            ),
            $validation->street[0]
        );
        $this->assertEquals(
            $validate_with_warning->city_locality,
            $validation->city_locality
        );
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $validate_with_warning->postal_code);
        $this->assertEquals(
            $validate_with_warning->postal_code,
            $validation->postal_code
        );
        $this->assertEquals(
            $validate_with_warning->country_code,
            $validation->country_code
        );
        $this->assertTrue($validation->isResidential);
        $this->assertEmpty($validation->errors);
        $this->assertNotEmpty($validation->warnings);
        $this->assertEquals('partially_verified_to_premise_level', $validation->warnings[0]['code']);
        $this->assertIsString($validation->warnings[0]['message']);
        $this->assertEquals(
            <<<'EOT'
This address has been verified down to the house/building level (highest possible accuracy with the provided data)
EOT,
            $validation->warnings[0]['message']
        );
    }

    /**
     * Tests a validation with `error` messages.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is "Invalid address. At least one address line is required.".
     */
    public function testNoAddressLinesValidationError(): void
    {
        try {
            new Address(
                array(
                    'street' => array(),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                )
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['errorCode']);
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
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is "Invalid address. No more than 3 street lines are allowed.".
     */
    public function testTooManyAddressLinesValidationError(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor', 'Clubhouse Level'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                )
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::INVALID_FIELD_VALUE, $error['errorCode']);
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
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testMissingCity(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => '',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                )
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['errorCode']);
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
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testMissingStatePostalAndCity(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => '',
                    'state_province' => '',
                    'postal_code' => '',
                    'country_code' => 'US',
                )
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['errorCode']);
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
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testMissingPostalCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '',
                    'country_code' => 'US',
                )
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['errorCode']);
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
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. The country_code must be specified.".
     */
    public function testMissingCountryCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => '',
                )
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::INVALID_FIELD_VALUE, $error['errorCode']);
            $this->assertEquals(
                'Invalid address. The country_code must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests a validation with invalid `country_code`.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. XX is not a valid country_code code."
     * (where XX is the value that was specified).
     */
    public function testInvalidCountryCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'USA',
                )
            );
            $this->expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::INVALID_FIELD_VALUE, $error['errorCode']);
            $this->assertEquals(
                "Invalid address. USA is not a valid country_code code.",
                $error['message']
            );
        }
    }

    public function testServerSideError(): void
    {
        try {
            $get_rpc_server_error = new Address(
                array(
                    'street' => array('500 Server Error'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                )
            );
            self::$shipengine->validateAddresses($get_rpc_server_error);
        } catch (SystemException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(SystemException::class, $e);
            $this->assertNotEmpty($error['requestId']);
            $this->assertStringStartsWith('req_', $error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::SYSTEM, $error['type']);
            $this->assertEquals(ErrorCode::UNSPECIFIED, $error['errorCode']);
            $this->assertEquals(
                "Unable to connect to the database",
                $error['message']
            );
        }
    }

    public function testNoNameCompanyPhone(): void
    {
        $goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddresses($goodAddress);

        $this->assertTrue($validation->status);
        $this->assertNotEmpty($validation);
        $this->assertEquals(
            strtoupper($goodAddress->street[0] . " " . $goodAddress->street[1]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($goodAddress->city_locality),
            $validation->city_locality
        );
        $this->assertEquals($goodAddress->state_province, $validation->state_province);
        $this->assertEquals($goodAddress->postal_code, $validation->postal_code);
        $this->assertEquals($goodAddress->country_code, $validation->country_code);
    }

    public function testWithNameCompanyPhone(): void
    {
        $address = self::$valid_address;
        $validation = self::$shipengine->validateAddresses($address);

        $this->assertTrue($validation->status);
        $this->assertEquals(
            strtoupper($address->name),
            $validation->matched_address->name
        );
        $this->assertEquals($address->phone, $validation->matched_address->phone);
        $this->assertEquals(
            strtoupper($address->company),
            $validation->matched_address->company
        );
        $this->assertEmpty($validation->messages);
    }

    public function testJsonSerialize(): void
    {
        $goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $this->assertIsArray(self::$shipengine->validateAddresses($goodAddress)->jsonSerialize());
    }

    public function addressObjectAssertions($object): void
    {
        $this->assertInstanceOf(Address::class, $object);
        $this->assertObjectHasAttribute('street', $object);
        $this->assertObjectHasAttribute('city_locality', $object);
        $this->assertObjectHasAttribute('state_province', $object);
        $this->assertObjectHasAttribute('postal_code', $object);
        $this->assertObjectHasAttribute('country_code', $object);
        $this->assertObjectHasAttribute('name', $object);
        $this->assertObjectHasAttribute('phone', $object);
        $this->assertObjectHasAttribute('company', $object);
    }

    public function addressValidateResultAssertions($object): void
    {
        $this->assertInstanceOf(AddressValidateResult::class, $object);
        $this->assertObjectHasAttribute('isValid', $object);
        $this->assertObjectHasAttribute('normalizedAddress', $object);
        $this->assertObjectHasAttribute('info', $object);
        $this->assertObjectHasAttribute('warnings', $object);
        $this->assertObjectHasAttribute('errors', $object);
    }
}
