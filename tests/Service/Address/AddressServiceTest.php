<?php

declare(strict_types=1);

namespace Service\Address;

use DateInterval;
use PHPUnit\Framework\TestCase;
use ShipEngine\Message\ShipEngineException;
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
                'apiKey' => 'baz',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
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
                'street' => array('4 Jersey St', 'ste 200'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($goodAddress);
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
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
                'isResidential' => null
            )
        );
        $validation = self::$shipengine->validateAddress($valid_residential_address);

        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            strtoupper(
                str_replace(
                    '.',
                    '',
                    $valid_residential_address->street[0] . " " . $valid_residential_address->street[1]
                )
            ),
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            strtoupper($valid_residential_address->cityLocality),
            $validation->normalizedAddress->cityLocality
        );
        $this->assertEquals(
            $valid_residential_address->stateProvince,
            $validation->normalizedAddress->stateProvince
        );
        $this->assertEquals(
            $valid_residential_address->postalCode,
            $validation->normalizedAddress->postalCode
        );
        $this->assertEquals(
            $valid_residential_address->countryCode,
            $validation->normalizedAddress->countryCode
        );
        $this->assertTrue($validation->normalizedAddress->isResidential);
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
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($goodAddress);

        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            strtoupper($goodAddress->street[0] . " " . $goodAddress->street[1]),
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            strtoupper($goodAddress->cityLocality),
            $validation->normalizedAddress->cityLocality
        );
        $this->assertEquals(
            $goodAddress->stateProvince,
            $validation->normalizedAddress->stateProvince
        );
        $this->assertEquals(
            $goodAddress->postalCode,
            $validation->normalizedAddress->postalCode
        );
        $this->assertEquals(
            $goodAddress->countryCode,
            $validation->normalizedAddress->countryCode
        );
        $this->assertFalse($validation->normalizedAddress->isResidential);
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
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US'
            )
        );
        $validation = self::$shipengine->validateAddress($unknown_address_type);

        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            strtoupper($unknown_address_type->street[0]),
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            strtoupper($unknown_address_type->cityLocality),
            $validation->normalizedAddress->cityLocality
        );
        $this->assertEquals(
            $unknown_address_type->stateProvince,
            $validation->normalizedAddress->stateProvince
        );
        $this->assertEquals(
            $unknown_address_type->postalCode,
            $validation->normalizedAddress->postalCode
        );
        $this->assertEquals(
            $unknown_address_type->countryCode,
            $validation->normalizedAddress->countryCode
        );
        $this->assertNull($validation->normalizedAddress->isResidential);
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
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($multi_line_address);

        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertArrayHasKey(0, $validation->normalizedAddress->street);
        $this->assertArrayHasKey(1, $validation->normalizedAddress->street);
        $this->assertEquals(
            strtoupper($multi_line_address->street[0] . ' ' . $multi_line_address->street[1]),
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            strtoupper($multi_line_address->cityLocality),
            $validation->normalizedAddress->cityLocality
        );
        $this->assertEquals(
            $multi_line_address->stateProvince,
            $validation->normalizedAddress->stateProvince
        );
        $this->assertEquals(
            $multi_line_address->postalCode,
            $validation->normalizedAddress->postalCode
        );
        $this->assertEquals(
            $multi_line_address->countryCode,
            $validation->normalizedAddress->countryCode
        );
        $this->assertFalse($validation->normalizedAddress->isResidential);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests with that the `postal-code` is numeric and matches the postalCode passed in.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * **postalCode** is numeric and matches original postalCode.
     * - **isResidential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testNumericPostalCode(): void
    {
        $goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($goodAddress);

        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            strtoupper($goodAddress->street[0] . " " . $goodAddress->street[1]),
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            strtoupper($goodAddress->cityLocality),
            $validation->normalizedAddress->cityLocality
        );
        $this->assertIsNumeric($goodAddress->postalCode);
        $this->assertEquals($goodAddress->postalCode, $validation->normalizedAddress->postalCode);
        $this->assertEquals($goodAddress->countryCode, $validation->normalizedAddress->countryCode);
        $this->assertFalse($validation->normalizedAddress->isResidential);
        $this->assertEmpty($validation->errors);
        $this->assertEmpty($validation->warnings);
    }

    /**
     * Tests with that the `postal-code` is alpha-numeric and matches the postalCode passed in.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **postalCode** is alpha-numeric and matches the original address.
     * - **isResidential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testAlphaPostalCode(): void
    {
        $canadaAddress = new Address(
            array(
                'street' => array('170 Princes Blvd', 'Ste 200'),
                'cityLocality' => 'Toronto',
                'stateProvince' => 'ON',
                'postalCode' => 'M6K 3C3',
                'countryCode' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddress($canadaAddress);

        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            $canadaAddress->street[0] . " " . $canadaAddress->street[1],
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            $canadaAddress->cityLocality,
            $validation->normalizedAddress->cityLocality
        );
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $canadaAddress->postalCode);
        $this->assertEquals('M6 K 3 C3', $validation->normalizedAddress->postalCode);
        $this->assertEquals($canadaAddress->countryCode, $validation->normalizedAddress->countryCode);
        $this->assertFalse($validation->normalizedAddress->isResidential);
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
                'cityLocality' => '南区',
                'stateProvince' => '京都',
                'postalCode' => '601-8104',
                'countryCode' => 'JP'
            )
        );
        $validation = self::$shipengine->validateAddress($non_latin_chars_address);

        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals('68 Kamitobatsunodacho', $validation->normalizedAddress->street[0]);
        $this->assertEquals(
            'Kyoto-Shi Minami-Ku',
            $validation->normalizedAddress->cityLocality
        );
        $this->assertEquals('Kyoto', $validation->normalizedAddress->stateProvince);
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9-]*$/',
            $non_latin_chars_address->postalCode
        );
        $this->assertEquals(
            $non_latin_chars_address->postalCode,
            $validation->normalizedAddress->postalCode
        );
        $this->assertEquals(
            $non_latin_chars_address->countryCode,
            $validation->normalizedAddress->countryCode
        );
        $this->assertFalse($validation->normalizedAddress->isResidential);
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
                'cityLocality' => 'Toronto',
                'stateProvince' => 'ON',
                'postalCode' => 'M6K 3C3',
                'countryCode' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddress($validate_with_error);

        $this->assertFalse($validation->isValid);
        $this->assertNull($validation->normalizedAddress);
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
                'cityLocality' => 'Toronto',
                'stateProvince' => 'ON',
                'postalCode' => 'M6K 3C3',
                'countryCode' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddress($validate_with_warning);

        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            $validate_with_warning->street[0] . " " . str_replace(
                'Apartment',
                'Apt',
                $validate_with_warning->street[1]
            ),
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            $validate_with_warning->cityLocality,
            $validation->normalizedAddress->cityLocality
        );
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $validate_with_warning->postalCode);
        $this->assertEquals(
            $validate_with_warning->postalCode,
            $validation->normalizedAddress->postalCode
        );
        $this->assertEquals(
            $validate_with_warning->countryCode,
            $validation->normalizedAddress->countryCode
        );
        $this->assertTrue($validation->normalizedAddress->isResidential);
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
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US',
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
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US',
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
                    'cityLocality' => '',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US',
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
                    'cityLocality' => '',
                    'stateProvince' => '',
                    'postalCode' => '',
                    'countryCode' => 'US',
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
     * Tests a validation with missing `postalCode`.
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
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '',
                    'countryCode' => 'US',
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
     * Tests a validation with missing `countryCode`.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. The countryCode must be specified.".
     */
    public function testMissingCountryCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => '',
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
                'Invalid address. The countryCode must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests a validation with invalid `countryCode`.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. XX is not a valid countryCode code."
     * (where XX is the value that was specified).
     */
    public function testInvalidCountryCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'USA',
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
                "Invalid address. USA is not a valid countryCode code.",
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
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US',
                )
            );
            self::$shipengine->validateAddress($get_rpc_server_error);
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
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($goodAddress);

        $this->assertTrue($validation->isValid);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            strtoupper($goodAddress->street[0] . " " . $goodAddress->street[1]),
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            strtoupper($goodAddress->cityLocality),
            $validation->normalizedAddress->cityLocality
        );
        $this->assertEquals($goodAddress->stateProvince, $validation->normalizedAddress->stateProvince);
        $this->assertEquals($goodAddress->postalCode, $validation->normalizedAddress->postalCode);
        $this->assertEquals($goodAddress->countryCode, $validation->normalizedAddress->countryCode);
        //        $this->assertFalse(
        //array_key_exists($validation->normalizedAddress->name,
        // $validation->normalizedAddress)
        //);
        //        $this->assertFalse(
        //array_key_exists($validation->normalizedAddress->phone,
        // $validation->normalizedAddress)
        //);
        //        $this->assertFalse(
        //array_key_exists($validation->normalizedAddress['company'],
        // $validation->normalizedAddress)
        //);
    }

    public function testWithNameCompanyPhone(): void
    {
        $address = new Address(
            array(
                'street' => array(
                    '4 Jersey St',
                    'ste 200'
                ),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
                'isResidential' => false,
                'name' => 'Bruce Wayne',
                'phone' => '1234567891',
                'company' => 'ShipEngine'
            )
        );
        $validation = self::$shipengine->validateAddress($address);

        $this->assertTrue($validation->isValid);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            strtoupper($address->name),
            $validation->normalizedAddress->name
        );
        $this->assertEquals($address->phone, $validation->normalizedAddress->phone);
        $this->assertEquals(
            strtoupper($address->company),
            $validation->normalizedAddress->company
        );
        $this->assertEmpty($validation->warnings);
        $this->assertEmpty($validation->errors);
    }

    // Normalize Address Tests
    public function testNormalizeAddressReturnType(): void
    {
        $goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $this->assertInstanceOf(Address::class, self::$shipengine->normalizeAddress($goodAddress));
    }

    /**
     * Tests the `normalizeAddress` method with a valid isResidential address.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **address** is returned and matches the given address.
     * - **isResidential** flag on the normalized address is set to `true`.
     */
    public function testNormalizeValidResidentialAddress(): void
    {
        $valid_residential_address = new Address(
            array(
                'street' => array('4 Jersey St', 'Apt. 2b'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US'
            )
        );
        $validation = self::$shipengine->normalizeAddress($valid_residential_address);

        $this->addressObjectAssertions($validation);
        $this->assertNotNull($validation);
        $this->assertTrue($validation->isResidential);
        $this->assertEquals(
            str_replace(
                '.',
                '',
                strtoupper($valid_residential_address->street[0] . " " . $valid_residential_address->street[1])
            ),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($valid_residential_address->cityLocality),
            $validation->cityLocality
        );
        $this->assertEquals(
            $valid_residential_address->stateProvince,
            $validation->stateProvince
        );
        $this->assertEquals(
            $valid_residential_address->postalCode,
            $validation->postalCode
        );
        $this->assertEquals(
            $valid_residential_address->countryCode,
            $validation->countryCode
        );
    }

    /**
     * Tests the `normalizeAddress` method with a valid commercial address.
     *
     * `Assertions:`
     * - **valid** flag is `false`.
     * - **address** is returned and matches the given address.
     * - **isResidential** flag on the normalized address is set to `true`.
     */
    public function testNormalizeValidCommercialAddress(): void
    {
        $goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $validation = self::$shipengine->normalizeAddress($goodAddress);

        $this->addressObjectAssertions($validation);
        $this->assertNotNull($validation);
        $this->assertFalse($validation->isResidential);
        $this->assertEquals(
            strtoupper($goodAddress->street[0] . " " . $goodAddress->street[1]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($goodAddress->cityLocality),
            $validation->cityLocality
        );
        $this->assertEquals(
            $goodAddress->stateProvince,
            $validation->stateProvince
        );
        $this->assertEquals(
            $goodAddress->postalCode,
            $validation->postalCode
        );
        $this->assertEquals(
            $goodAddress->countryCode,
            $validation->countryCode
        );
    }

    /**
     * Tests the `normalizeAddress` method with a valid address of unknown type (e.g. isResidential vs commercial).
     *
     * `Assertions:`
     * - **valid** flag is `false`.
     * - **address** is returned and matches the given address.
     * - **isResidential** flag on the normalized address is set to `null`.
     */
    public function testNormalizeValidAddressUnknownType(): void
    {
        $unknown_address_type = new Address(
            array(
                'street' => array('4 Jersey St'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US'
            )
        );
        $validation = self::$shipengine->normalizeAddress($unknown_address_type);

        $this->addressObjectAssertions($validation);
        $this->assertNotNull($validation);
        $this->assertNull($validation->isResidential);
        $this->assertEquals(
            strtoupper($unknown_address_type->street[0]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($unknown_address_type->cityLocality),
            $validation->cityLocality
        );
        $this->assertEquals(
            $unknown_address_type->stateProvince,
            $validation->stateProvince
        );
        $this->assertEquals(
            $unknown_address_type->postalCode,
            $validation->postalCode
        );
        $this->assertEquals(
            $unknown_address_type->countryCode,
            $validation->countryCode
        );
    }

    /**
     * Tests `normalizeAddress` with an `multi-line address` or an address that has values for `address_line1`,
     * `address_line2`, and `address_line3` in the parameters of the request.
     *
     * `Assertions:`
     * - Array keys passed in are the keys we get back include the necessary normalization
     * provided by ShipEngine API.
     * - **address** is returned and matches the given address.
     * - **isResidential** flag on the normalized address is set to `false`.
     */
    public function testNormalizeAddressWithMultiLineAddress(): void
    {
        $multi_line_address = new Address(
            array(
                'street' => array(
                    '4 Jersey St',
                    'ste 200',
                    '2nd Floor'
                ),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $validation = self::$shipengine->normalizeAddress($multi_line_address);

        $this->addressObjectAssertions($validation);
        $this->assertArrayHasKey(0, $validation->street);
        $this->assertArrayHasKey(1, $validation->street);
        $this->assertArrayNotHasKey(3, $validation->street);
        $this->assertEquals(
            strtoupper($multi_line_address->street[0] . ' ' . $multi_line_address->street[1]),
            $validation->street[0]
        );
        $this->assertEquals(
            strtoupper($multi_line_address->cityLocality),
            $validation->cityLocality
        );
        $this->assertEquals(
            $multi_line_address->stateProvince,
            $validation->stateProvince
        );
        $this->assertEquals(
            $multi_line_address->postalCode,
            $validation->postalCode
        );
        $this->assertEquals(
            $multi_line_address->countryCode,
            $validation->countryCode
        );
        $this->assertFalse($validation->isResidential);
    }

    /**
     * Tests `normalizeAddress` where the `postal-code` on the given address is
     * numeric and matches the postalCode passed in.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * **postalCode** is numeric and matches original postalCode.
     * - **isResidential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testNormalizeAddressWithNumericPostalCode(): void
    {
        $goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($goodAddress);

        $this->addressValidateResultAssertions($validation);
        $this->assertTrue($validation->isValid);
        $this->assertEquals(
            strtoupper($goodAddress->street[0] . " " . $goodAddress->street[1]),
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            strtoupper($goodAddress->cityLocality),
            $validation->normalizedAddress->cityLocality
        );
        $this->assertIsNumeric($goodAddress->postalCode);
        $this->assertEquals($goodAddress->postalCode, $validation->normalizedAddress->postalCode);
        $this->assertEquals($goodAddress->countryCode, $validation->normalizedAddress->countryCode);
        $this->assertFalse($validation->normalizedAddress->isResidential);
    }

    /**
     * Tests `normalizeAddress` with that the `postal-code` is alpha and matches the postalCode passed in.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **postalCode** is alpha and matches the original address.
     * - **isResidential** flag on the normalized address is `false`.
     */
    public function testNormalizeAddressWithAlphaPostalCode(): void
    {
        $canadaAddress = new Address(
            array(
                'street' => array('170 Princes Blvd', 'Ste 200'),
                'cityLocality' => 'Toronto',
                'stateProvince' => 'ON',
                'postalCode' => 'M6K 3C3',
                'countryCode' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddress($canadaAddress);

        $this->addressValidateResultAssertions($validation);
        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            $canadaAddress->street[0] . " " . $canadaAddress->street[1],
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            $canadaAddress->cityLocality,
            $validation->normalizedAddress->cityLocality
        );
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $canadaAddress->postalCode);
        $this->assertEquals('M6 K 3 C3', $validation->normalizedAddress->postalCode);
        $this->assertEquals($canadaAddress->countryCode, $validation->normalizedAddress->countryCode);
        $this->assertFalse($validation->normalizedAddress->isResidential);
    }

    /**
     * Tests `normalizeAddress` as address with non-latin characters and confirms the normalization.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **normalized address** has proper normalization applied to non-latin characters.
     * - **isResidential** flag on the normalized address is `false`.
     */
    public function testNormalizeAddressWithNonLatinCharacters(): void
    {
        $non_latin_chars_address = new Address(
            array(
                'street' => array(
                    '上鳥羽角田町６８'
                ),
                'cityLocality' => '南区',
                'stateProvince' => '京都',
                'postalCode' => '601-8104',
                'countryCode' => 'JP'
            )
        );
        $validation = self::$shipengine->validateAddress($non_latin_chars_address);

        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            '68 Kamitobatsunodacho',
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            'Kyoto-Shi Minami-Ku',
            $validation->normalizedAddress->cityLocality
        );

        $this->assertEquals('Kyoto', $validation->normalizedAddress->stateProvince);
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9-]*$/',
            $non_latin_chars_address->postalCode
        );
        $this->assertEquals(
            $non_latin_chars_address->postalCode,
            $validation->normalizedAddress->postalCode
        );
        $this->assertEquals(
            $non_latin_chars_address->countryCode,
            $validation->normalizedAddress->countryCode
        );

        $this->assertFalse($validation->normalizedAddress->isResidential);
    }

    /**
     * Tests `normalizeAddress` a validation with `warning` messages.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **isResidential** flag on the normalized address is `true`.
     * - That **warning** messages are provided.
     * - There are no **error** messages.
     */
    public function testNormalizeAddressWithWarning(): void
    {
        $validate_with_warning = new Address(
            array(
                'street' => array('170 Warning Blvd', 'Apartment 32-B'),
                'cityLocality' => 'Toronto',
                'stateProvince' => 'ON',
                'postalCode' => 'M6K 3C3',
                'countryCode' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddress($validate_with_warning);

        $this->addressValidateResultAssertions($validation);
        $this->assertTrue($validation->isValid);
        $this->assertInstanceOf(Address::class, $validation->normalizedAddress);
        $this->assertNotEmpty($validation->normalizedAddress);
        $this->assertEquals(
            $validate_with_warning->street[0] . " " . str_replace(
                'Apartment',
                'Apt',
                $validate_with_warning->street[1]
            ),
            $validation->normalizedAddress->street[0]
        );
        $this->assertEquals(
            $validate_with_warning->cityLocality,
            $validation->normalizedAddress->cityLocality
        );
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $validate_with_warning->postalCode);
        $this->assertEquals(
            $validate_with_warning->postalCode,
            $validation->normalizedAddress->postalCode
        );
        $this->assertEquals(
            $validate_with_warning->countryCode,
            $validation->normalizedAddress->countryCode
        );
        $this->assertTrue($validation->normalizedAddress->isResidential);
        $this->assertEmpty($validation->errors);
        $this->assertNotEmpty($validation->warnings);
        $this->assertIsString($validation->warnings[0]['message']);
        $this->assertEquals(
            <<<'EOT'
This address has been verified down to the house/building level (highest possible accuracy with the provided data)
EOT,
            $validation->warnings[0]['message']
        );
    }

    /**
     * Tests `normalizeAddress` a validation with `error` messages.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is "Invalid address. At least one address line is required.".
     */
    public function testNormalizeAddressWithNoAddressLines(): void
    {
        try {
            new Address(
                array(
                    'street' => array(),
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US',
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
     * Tests `normalizeAddress` a validation with too many address lines.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is "Invalid address. No more than 3 street lines are allowed.".
     */
    public function testNormalizeAddressWithTooManyAddressLines(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor', 'Clubhouse Level'),
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US',
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
     * Tests `normalizeAddress` a validation with missing `state`.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testNormalizeAddressWithMissingStatePostalAndCity(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'cityLocality' => '',
                    'stateProvince' => '',
                    'postalCode' => '',
                    'countryCode' => 'US',
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
     * Tests `normalizeAddress` a validation with missing `city`.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testNormalizeAddressWithMissingCity(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'cityLocality' => '',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US',
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
     * Tests `normalizeAddress` a validation with missing `countryCode`.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. The countryCode must be specified.".
     */
    public function testNormalizeAddressWithMissingCountryCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => '',
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
                'Invalid address. The countryCode must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests `normalizeAddress` a validation with invalid `countryCode`.
     *
     * `Assertions:`
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. XX is not a valid countryCode code."
     * (where XX is the value that was specified).
     */
    public function testNormalizeAddressWithInvalidCountryCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'USA',
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
                "Invalid address. USA is not a valid countryCode code.",
                $error['message']
            );
        }
    }

    public function testNormalizeAddressWithServerSideError(): void
    {
        try {
            $get_invalid_address_error = new Address(
                array(
                    'street' => array('500 Server Error'),
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US'
                )
            );
            self::$shipengine->validateAddress($get_invalid_address_error);
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

    public function testNormalizeAddressWithErrorMessage(): void
    {
        try {
            $validate_with_error = new Address(
                array(
                    'street' => array('170 Error Blvd'),
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US'
                )
            );
            self::$shipengine->normalizeAddress($validate_with_error);
        } catch (ShipEngineException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ShipEngineException::class, $e);
            $this->assertNotEmpty($error['requestId']);
            $this->assertStringStartsWith('req_', $error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals('error', $error['type']);
            $this->assertEquals('minimum_postal_code_verification_failed', $error['errorCode']);
            $this->assertEquals(
                "Invalid address. Insufficient or inaccurate postal code",
                $error['message']
            );
        }
    }

    public function testNormalizeAddressWithMultipleErrorMessages(): void
    {
        try {
            $validate_with_error = new Address(
                array(
                    'street' => array('4 Invalid St',),
                    'cityLocality' => 'Boston',
                    'stateProvince' => 'MA',
                    'postalCode' => '02215',
                    'countryCode' => 'US'
                )
            );
            self::$shipengine->normalizeAddress($validate_with_error);
        } catch (ShipEngineException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ShipEngineException::class, $e);
            $this->assertNotEmpty($error['requestId']);
            $this->assertStringStartsWith('req_', $error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals('error', $error['type']);
            $this->assertEquals(ErrorCode::INVALID_ADDRESS, $error['errorCode']);
            $this->assertEquals(
                "Invalid address.\nInvalid City, State, or Zip\nInsufficient or Incorrect Address Data
\n",
                $error['message']
            );
        }
    }

    public function testJsonSerialize(): void
    {
        $goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
        $this->assertIsArray(self::$shipengine->validateAddress($goodAddress)->jsonSerialize());
    }

    public function addressObjectAssertions($object): void
    {
        $this->assertInstanceOf(Address::class, $object);
        $this->assertObjectHasAttribute('street', $object);
        $this->assertObjectHasAttribute('cityLocality', $object);
        $this->assertObjectHasAttribute('stateProvince', $object);
        $this->assertObjectHasAttribute('postalCode', $object);
        $this->assertObjectHasAttribute('countryCode', $object);
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
