<?php

declare(strict_types=1);

namespace Service\Address;

use DateInterval;
use PHPUnit\Framework\TestCase;
use ShipEngine\Message\BusinessRuleException;
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
     * Pass an `api-key` into the new instance of the *ShipEngine* class and instantiate fixtures.
     *
     * @beforeClass
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        putenv('CLIENT_BASE_URI=https://simengine.herokuapp.com/jsonrpc');
        self::$shipengine = new ShipEngine(array(
            'api_key' => 'baz',
            'base_url' => Endpoints::TEST_RPC_URL,
            'page_size' => 75,
            'retries' => 1,
            'timeout' => new DateInterval('PT15000S')
        ));
    }

    /**
     * Test the return type, should be an instance of the `AddressTest` Type.
     */
    public function testReturnType(): void
    {
        $good_address = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($good_address);
        self::assertInstanceOf(AddressValidateResult::class, $validation);
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
        $valid_residential_address = new Address(
            array(
                'street' => array('4 Jersey St', 'validate-residential-address'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US'
            )
        );
        $validation = self::$shipengine->validateAddress($valid_residential_address);

        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals(
            strtoupper($valid_residential_address->street[0]),
            $validation->normalized_address->street[0]
        );
        self::assertEquals(
            strtoupper($valid_residential_address->city_locality),
            $validation->normalized_address->city_locality
        );
        self::assertEquals(
            $valid_residential_address->state_province,
            $validation->normalized_address->state_province
        );
        self::assertEquals(
            $valid_residential_address->postal_code,
            $validation->normalized_address->postal_code
        );
        self::assertEquals(
            $valid_residential_address->country_code,
            $validation->normalized_address->country_code
        );
        self::assertTrue($validation->normalized_address->residential);
        self::assertEmpty($validation->errors);
        self::assertEmpty($validation->warnings);
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
        $good_address = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($good_address);

        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals(
            strtoupper($good_address->street[0]),
            $validation->normalized_address->street[0]
        );
        self::assertEquals(
            strtoupper($good_address->city_locality),
            $validation->normalized_address->city_locality
        );
        self::assertEquals(
            $good_address->state_province,
            $validation->normalized_address->state_province
        );
        self::assertEquals(
            $good_address->postal_code,
            $validation->normalized_address->postal_code
        );
        self::assertEquals(
            $good_address->country_code,
            $validation->normalized_address->country_code
        );
        self::assertFalse($validation->normalized_address->residential);
        self::assertEmpty($validation->errors);
        self::assertEmpty($validation->warnings);
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
        $unknown_address_type = new Address(
            array(
                'street' => array('4 Jersey St', 'validate-unknown-address'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US'
            )
        );
        $validation = self::$shipengine->validateAddress($unknown_address_type);

        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals(
            strtoupper($unknown_address_type->street[0]),
            $validation->normalized_address->street[0]
        );
        self::assertEquals(
            strtoupper($unknown_address_type->city_locality),
            $validation->normalized_address->city_locality
        );
        self::assertEquals(
            $unknown_address_type->state_province,
            $validation->normalized_address->state_province
        );
        self::assertEquals(
            $unknown_address_type->postal_code,
            $validation->normalized_address->postal_code
        );
        self::assertEquals(
            $unknown_address_type->country_code,
            $validation->normalized_address->country_code
        );
        self::assertNull($validation->normalized_address->residential);
        self::assertEmpty($validation->errors);
        self::assertEmpty($validation->warnings);
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
        $multi_line_address = new Address(
            array(
                'street' => array(
                    '4 Jersey St',
                    'ste 200',
                    'multi-line-address'
                ),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($multi_line_address);

        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertArrayHasKey(0, $validation->normalized_address->street);
        self::assertArrayHasKey(1, $validation->normalized_address->street);
        self::assertArrayNotHasKey(3, $validation->normalized_address->street);
        self::assertEquals(
            strtoupper($multi_line_address->street[0] . ' ' . $multi_line_address->street[1]),
            $validation->normalized_address->street[0]
        );
        self::assertEquals(
            strtoupper($multi_line_address->city_locality),
            $validation->normalized_address->city_locality
        );
        self::assertEquals(
            $multi_line_address->state_province,
            $validation->normalized_address->state_province
        );
        self::assertEquals(
            $multi_line_address->postal_code,
            $validation->normalized_address->postal_code
        );
        self::assertEquals(
            $multi_line_address->country_code,
            $validation->normalized_address->country_code
        );
        self::assertFalse($validation->normalized_address->residential);
        self::assertEmpty($validation->errors);
        self::assertEmpty($validation->warnings);
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
        $good_address = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($good_address);

        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals(
            strtoupper($good_address->street[0]),
            $validation->normalized_address->street[0]
        );
        self::assertEquals(
            strtoupper($good_address->city_locality),
            $validation->normalized_address->city_locality
        );
        self::assertIsNumeric($good_address->postal_code);
        self::assertEquals($good_address->postal_code, $validation->normalized_address->postal_code);
        self::assertEquals($good_address->country_code, $validation->normalized_address->country_code);
        self::assertFalse($validation->normalized_address->residential);
        self::assertEmpty($validation->errors);
        self::assertEmpty($validation->warnings);
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
        $canada_address = new Address(
            array(
                'street' => array('170 Princes\' Blvd', 'validate-canadian-address'),
                'city_locality' => 'Toronto',
                'state_province' => 'ON',
                'postal_code' => 'M6K 3C3',
                'country_code' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddress($canada_address);

        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals($canada_address->street[0], $validation->normalized_address->street[0]);
        self::assertEquals(
            $canada_address->city_locality,
            $validation->normalized_address->city_locality
        );
        self::assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $canada_address->postal_code);
        self::assertEquals($canada_address->postal_code, $validation->normalized_address->postal_code);
        self::assertEquals($canada_address->country_code, $validation->normalized_address->country_code);
        self::assertFalse($validation->normalized_address->residential);
        self::assertEmpty($validation->errors);
        self::assertEmpty($validation->warnings);
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
    public function testAddressWithNonLatinCharacters(): void
    {
        $non_latin_chars_address = new Address(
            array(
                'street' => array(
                    '上鳥羽角田町６８',
                    'validate-with-non-latin-chars'
                ),
                'city_locality' => '南区',
                'state_province' => '京都',
                'postal_code' => '601-8104',
                'country_code' => 'JP'
            )
        );
        $validation = self::$shipengine->validateAddress($non_latin_chars_address);

        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals('68 Kamitobatsunodacho', $validation->normalized_address->street[0]);
        self::assertEquals(
            'Kyoto-Shi Minami-Ku',
            $validation->normalized_address->city_locality
        );
        self::assertEquals('Kyoto', $validation->normalized_address->state_province);
        self::assertMatchesRegularExpression(
            '/^[a-zA-Z0-9-]*$/',
            $non_latin_chars_address->postal_code
        );
        self::assertEquals(
            $non_latin_chars_address->postal_code,
            $validation->normalized_address->postal_code
        );
        self::assertEquals(
            $non_latin_chars_address->country_code,
            $validation->normalized_address->country_code
        );
        self::assertFalse($validation->normalized_address->residential);
        self::assertEmpty($validation->errors);
        self::assertEmpty($validation->warnings);
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
        $validate_with_error = new Address(
            array(
                'street' => array('124 Conch St', 'validate-with-error'),
                'city_locality' => 'Bikini Bottom',
                'state_province' => 'Pacific Ocean',
                'postal_code' => '4A6 G67',
                'country_code' => 'US'
            )
        );
        $validation = self::$shipengine->validateAddress($validate_with_error);

        self::assertFalse($validation->valid);
        self::assertNull($validation->normalized_address);
        self::assertNotEmpty($validation->errors);
        self::assertIsArray($validation->errors);
        self::assertIsString($validation->errors[0]);
        self::assertEmpty($validation->warnings);
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
        $validate_with_warning = new Address(
            array(
                'street' => array('170 Princes\' Blvd', 'validate-with-warning'),
                'city_locality' => 'Toronto',
                'state_province' => 'ON',
                'postal_code' => 'M6K 3C3',
                'country_code' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddress($validate_with_warning);

        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals($validate_with_warning->street[0], $validation->normalized_address->street[0]);
        self::assertEquals(
            $validate_with_warning->city_locality,
            $validation->normalized_address->city_locality
        );
        self::assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $validate_with_warning->postal_code);
        self::assertEquals(
            $validate_with_warning->postal_code,
            $validation->normalized_address->postal_code
        );
        self::assertEquals(
            $validate_with_warning->country_code,
            $validation->normalized_address->country_code
        );
        self::assertFalse($validation->normalized_address->residential);
        self::assertEmpty($validation->errors);
        self::assertNotEmpty($validation->warnings);
        self::assertIsString($validation->warnings[0]);
        self::assertEquals(
            <<<'EOT'
This address has been verified down to the house/building level (highest possible accuracy with the provided data)
EOT,
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
            new Address(
                array(
                    'street' => array(),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                )
            );
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['error_code']);
            self::assertEquals(
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
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor', 'Clubhouse Level'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                )
            );
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::INVALID_FIELD_VALUE, $error['error_code']);
            self::assertEquals(
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
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => '',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                )
            );
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['error_code']);
            self::assertEquals(
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
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => '',
                    'state_province' => '',
                    'postal_code' => '',
                    'country_code' => 'US',
                )
            );
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['error_code']);
            self::assertEquals(
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
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '',
                    'country_code' => 'US',
                )
            );
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['error_code']);
            self::assertEquals(
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
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => '',
                )
            );
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::INVALID_FIELD_VALUE, $error['error_code']);
            self::assertEquals(
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
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'USA',
                )
            );
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::INVALID_FIELD_VALUE, $error['error_code']);
            self::assertEquals(
                "Invalid address. USA is not a valid country code.",
                $error['message']
            );
        }
    }

    public function testServerSideError()
    {
        try {
            $get_rpc_server_error = new Address(
                array(
                    'street' => array('4 Jersey St', 'ste 200', 'rpc-server-error'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                )
            );
            self::$shipengine->validateAddress($get_rpc_server_error);
        } catch (SystemException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(SystemException::class, $e);
            self::assertNotEmpty($error['request_id']);
            self::assertStringStartsWith('req_', $error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::SYSTEM, $error['type']);
            self::assertEquals(ErrorCode::UNSPECIFIED, $error['error_code']);
            self::assertEquals(
                "Unable to connect to the database",
                $error['message']
            );
        }
    }

    public function testNoNameCompanyPhone()
    {
        $good_address = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($good_address);

        self::assertTrue($validation->valid);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals(
            strtoupper($good_address->street[0]),
            $validation->normalized_address->street[0]
        );
        self::assertEquals(
            strtoupper($good_address->city_locality),
            $validation->normalized_address->city_locality
        );
        self::assertEquals($good_address->state_province, $validation->normalized_address->state_province);
        self::assertEquals($good_address->postal_code, $validation->normalized_address->postal_code);
        self::assertEquals($good_address->country_code, $validation->normalized_address->country_code);
        //        self::assertFalse(
        //array_key_exists($validation->normalized_address->name,
        // $validation->normalized_address)
        //);
        //        self::assertFalse(
        //array_key_exists($validation->normalized_address->phone,
        // $validation->normalized_address)
        //);
        //        self::assertFalse(
        //array_key_exists($validation->normalized_address['company'],
        // $validation->normalized_address)
        //);
    }

    public function testWithNameCompanyPhone()
    {
        $address = new Address(
            array(
                'street' => array(
                    '4 Jersey St',
                    'ste 200',
                    'validate-with-personal-info'
                ),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
                'residential' => false,
                'name' => 'Bruce Wayne',
                'phone' => '1234567891',
                'company_name' => 'ShipEngine'
            )
        );
        $validation = self::$shipengine->validateAddress($address);

        self::assertTrue($validation->valid);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals(
            strtoupper($address->name),
            $validation->normalized_address->name
        );
        self::assertEquals($address->phone, $validation->normalized_address->phone);
        self::assertEquals(
            strtoupper($address->company),
            $validation->normalized_address->company
        );
        self::assertEmpty($validation->warnings);
        self::assertEmpty($validation->errors);
    }

    // Normalize Address Tests
    public function testNormalizeAddressReturnType()
    {
        $good_address = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        self::assertInstanceOf(Address::class, self::$shipengine->normalizeAddress($good_address));
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
        $valid_residential_address = new Address(
            array(
                'street' => array('4 Jersey St', 'validate-residential-address'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US'
            )
        );
        $validation = self::$shipengine->normalizeAddress($valid_residential_address);

        $this->addressObjectAssertions($validation);
        self::assertNotNull($validation);
        self::assertTrue($validation->residential);
        self::assertEquals(
            strtoupper($valid_residential_address->street[0]),
            $validation->street[0]
        );
        self::assertEquals(
            strtoupper($valid_residential_address->city_locality),
            $validation->city_locality
        );
        self::assertEquals(
            $valid_residential_address->state_province,
            $validation->state_province
        );
        self::assertEquals(
            $valid_residential_address->postal_code,
            $validation->postal_code
        );
        self::assertEquals(
            $valid_residential_address->country_code,
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
        $good_address = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->normalizeAddress($good_address);

        $this->addressObjectAssertions($validation);
        self::assertNotNull($validation);
        self::assertFalse($validation->residential);
        self::assertEquals(
            strtoupper($good_address->street[0]),
            $validation->street[0]
        );
        self::assertEquals(
            strtoupper($good_address->city_locality),
            $validation->city_locality
        );
        self::assertEquals(
            $good_address->state_province,
            $validation->state_province
        );
        self::assertEquals(
            $good_address->postal_code,
            $validation->postal_code
        );
        self::assertEquals(
            $good_address->country_code,
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
        $unknown_address_type = new Address(
            array(
                'street' => array('4 Jersey St', 'validate-unknown-address'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US'
            )
        );
        $validation = self::$shipengine->normalizeAddress($unknown_address_type);

        $this->addressObjectAssertions($validation);
        self::assertNotNull($validation);
        self::assertNull($validation->residential);
        self::assertEquals(
            strtoupper($unknown_address_type->street[0]),
            $validation->street[0]
        );
        self::assertEquals(
            strtoupper($unknown_address_type->city_locality),
            $validation->city_locality
        );
        self::assertEquals(
            $unknown_address_type->state_province,
            $validation->state_province
        );
        self::assertEquals(
            $unknown_address_type->postal_code,
            $validation->postal_code
        );
        self::assertEquals(
            $unknown_address_type->country_code,
            $validation->country_code
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
     * - **residential** flag on the normalized address is set to `false`.
     */
    public function testNormalizeAddressWithMultiLineAddress()
    {
        $multi_line_address = new Address(
            array(
                'street' => array(
                    '4 Jersey St',
                    'ste 200',
                    'multi-line-address'
                ),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->normalizeAddress($multi_line_address);

        $this->addressObjectAssertions($validation);
        self::assertArrayHasKey(0, $validation->street);
        self::assertArrayHasKey(1, $validation->street);
        self::assertArrayNotHasKey(3, $validation->street);
        self::assertEquals(
            strtoupper($multi_line_address->street[0] . ' ' . $multi_line_address->street[1]),
            $validation->street[0]
        );
        self::assertEquals(
            strtoupper($multi_line_address->city_locality),
            $validation->city_locality
        );
        self::assertEquals(
            $multi_line_address->state_province,
            $validation->state_province
        );
        self::assertEquals(
            $multi_line_address->postal_code,
            $validation->postal_code
        );
        self::assertEquals(
            $multi_line_address->country_code,
            $validation->country_code
        );
        self::assertFalse($validation->residential);
    }

    /**
     * Tests `normalizeAddress` where the `postal-code` on the given address is
     * numeric and matches the postal_code passed in.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * **postal_code** is numeric and matches original postal_code.
     * - **residential** flag on the normalized address is `false`.
     * - There are no **warnings** and **errors** messages.
     */
    public function testNormalizeAddressWithNumericPostalCode(): void
    {
        $good_address = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        $validation = self::$shipengine->validateAddress($good_address);

        $this->addressValidateResultAssertions($validation);
        self::assertTrue($validation->valid);
        self::assertEquals(
            strtoupper($good_address->street[0]),
            $validation->normalized_address->street[0]
        );
        self::assertEquals(
            strtoupper($good_address->city_locality),
            $validation->normalized_address->city_locality
        );
        self::assertIsNumeric($good_address->postal_code);
        self::assertEquals($good_address->postal_code, $validation->normalized_address->postal_code);
        self::assertEquals($good_address->country_code, $validation->normalized_address->country_code);
        self::assertFalse($validation->normalized_address->residential);
    }

    /**
     * Tests `normalizeAddress` with that the `postal-code` is alpha and matches the postal_code passed in.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **postal_code** is alpha and matches the original address.
     * - **residential** flag on the normalized address is `false`.
     */
    public function testNormalizeAddressWithAlphaPostalCode(): void
    {
        $canada_address = new Address(
            array(
                'street' => array('170 Princes\' Blvd', 'validate-canadian-address'),
                'city_locality' => 'Toronto',
                'state_province' => 'ON',
                'postal_code' => 'M6K 3C3',
                'country_code' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddress($canada_address);

        $this->addressValidateResultAssertions($validation);
        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals($canada_address->street[0], $validation->normalized_address->street[0]);
        self::assertEquals(
            $canada_address->city_locality,
            $validation->normalized_address->city_locality
        );
        self::assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $canada_address->postal_code);
        self::assertEquals($canada_address->postal_code, $validation->normalized_address->postal_code);
        self::assertEquals($canada_address->country_code, $validation->normalized_address->country_code);
        self::assertFalse($validation->normalized_address->residential);
    }

    /**
     * Tests `normalizeAddress` as address with non-latin characters and confirms the normalization.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **normalized address** has proper normalization applied to non-latin characters.
     * - **residential** flag on the normalized address is `false`.
     */
    public function testNormalizeAddressWithNonLatinCharacters(): void
    {
        $non_latin_chars_address = new Address(
            array(
                'street' => array(
                    '上鳥羽角田町６８',
                    'validate-with-non-latin-chars'
                ),
                'city_locality' => '南区',
                'state_province' => '京都',
                'postal_code' => '601-8104',
                'country_code' => 'JP'
            )
        );
        $validation = self::$shipengine->validateAddress($non_latin_chars_address);

        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals(
            '68 Kamitobatsunodacho',
            $validation->normalized_address->street[0]
        );
        self::assertEquals(
            'Kyoto-Shi Minami-Ku',
            $validation->normalized_address->city_locality
        );

        self::assertEquals('Kyoto', $validation->normalized_address->state_province);
        self::assertMatchesRegularExpression(
            '/^[a-zA-Z0-9-]*$/',
            $non_latin_chars_address->postal_code
        );
        self::assertEquals(
            $non_latin_chars_address->postal_code,
            $validation->normalized_address->postal_code
        );
        self::assertEquals(
            $non_latin_chars_address->country_code,
            $validation->normalized_address->country_code
        );

        self::assertFalse($validation->normalized_address->residential);
    }

    /**
     * Tests `normalizeAddress` a validation with `warning` messages.
     *
     * `Assertions:`
     * - **valid** flag is `true`.
     * - **normalized address** is returned and matches the given address.
     * - **residential** flag on the normalized address is `false`.
     * - That **warning** messages are provided.
     * - There are no **error** messages.
     */
    public function testNormalizeAddressWithWarning()
    {
        $validate_with_warning = new Address(
            array(
                'street' => array('170 Princes\' Blvd', 'validate-with-warning'),
                'city_locality' => 'Toronto',
                'state_province' => 'ON',
                'postal_code' => 'M6K 3C3',
                'country_code' => 'CA',
            )
        );
        $validation = self::$shipengine->validateAddress($validate_with_warning);

        $this->addressValidateResultAssertions($validation);
        self::assertTrue($validation->valid);
        self::assertInstanceOf(Address::class, $validation->normalized_address);
        self::assertNotEmpty($validation->normalized_address);
        self::assertEquals($validate_with_warning->street[0], $validation->normalized_address->street[0]);
        self::assertEquals(
            $validate_with_warning->city_locality,
            $validation->normalized_address->city_locality
        );
        self::assertMatchesRegularExpression('/^[a-zA-Z0-9\s]*$/', $validate_with_warning->postal_code);
        self::assertEquals(
            $validate_with_warning->postal_code,
            $validation->normalized_address->postal_code
        );
        self::assertEquals(
            $validate_with_warning->country_code,
            $validation->normalized_address->country_code
        );
        self::assertFalse($validation->normalized_address->residential);
        self::assertEmpty($validation->errors);
        self::assertNotEmpty($validation->warnings);
        self::assertIsString($validation->warnings[0]);
        self::assertEquals(
            <<<'EOT'
This address has been verified down to the house/building level (highest possible accuracy with the provided data)
EOT,
            $validation->warnings[0]
        );
    }

    /**
     * Tests `normalizeAddress` a validation with `error` messages.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is "Invalid address. At least one address line is required.".
     */
    public function testNormalizeAddressWithNoAddressLines()
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
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['error_code']);
            self::assertEquals(
                'Invalid address. At least one address line is required.',
                $error['message']
            );
        }
    }

    /**
     * Tests `normalizeAddress` a validation with too many address lines.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is "Invalid address. No more than 3 street lines are allowed.".
     */
    public function testNormalizeAddressWithTooManyAddressLines()
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
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::INVALID_FIELD_VALUE, $error['error_code']);
            self::assertEquals(
                'Invalid address. No more than 3 street lines are allowed.',
                $error['message']
            );
        }
    }

    /**
     * Tests `normalizeAddress` a validation with missing `state`.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testNormalizeAddressWithMissingStatePostalAndCity()
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
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['error_code']);
            self::assertEquals(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests `normalizeAddress` a validation with missing `city`.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testNormalizeAddressWithMissingCity()
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
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['error_code']);
            self::assertEquals(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests `normalizeAddress` a validation with missing `country_code`.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. The country must be specified.".
     */
    public function testNormalizeAddressWithMissingCountryCode()
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
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::INVALID_FIELD_VALUE, $error['error_code']);
            self::assertEquals(
                'Invalid address. The country must be specified.',
                $error['message']
            );
        }
    }

    /**
     * Tests `normalizeAddress` a validation with invalid `country_code`.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **code** os `invalid_field_value`.
     * - **message** is "Invalid address. XX is not a valid country code."
     * (where XX is the value that was specified).
     */
    public function testNormalizeAddressWithInvalidCountryCode()
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
            self::expectException(ValidationException::class);
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(ValidationException::class, $e);
            self::assertNull($error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::VALIDATION, $error['type']);
            self::assertEquals(ErrorCode::INVALID_FIELD_VALUE, $error['error_code']);
            self::assertEquals(
                "Invalid address. USA is not a valid country code.",
                $error['message']
            );
        }
    }

    public function testNormalizeAddressWithServerSideError()
    {
        try {
            $get_invalid_address_error = new Address(
                array(
                    'street' => array('4 Jersey St', 'rpc-server-error'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US'
                )
            );
            self::$shipengine->validateAddress($get_invalid_address_error);
        } catch (SystemException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(SystemException::class, $e);
            self::assertNotEmpty($error['request_id']);
            self::assertStringStartsWith('req_', $error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::SYSTEM, $error['type']);
            self::assertEquals(ErrorCode::UNSPECIFIED, $error['error_code']);
            self::assertEquals(
                "Unable to connect to the database",
                $error['message']
            );
        }
    }

    public function testNormalizeAddressWithErrorMessage()
    {
        try {
            $validate_with_error = new Address(
                array(
                    'street' => array('4 Jersey St', 'validate-with-error'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US'
                )
            );
            self::$shipengine->normalizeAddress($validate_with_error);
        } catch (BusinessRuleException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(BusinessRuleException::class, $e);
            self::assertNotEmpty($error['request_id']);
            self::assertStringStartsWith('req_', $error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::BUSINESS_RULES, $error['type']);
            self::assertEquals(ErrorCode::INVALID_ADDRESS, $error['error_code']);
            self::assertEquals(
                "Invalid address. Invalid City, State, or Zip",
                $error['message']
            );
        }
    }

    public function testNormalizeAddressWithMultipleErrorMessages(): void
    {
        try {
            $validate_with_error = new Address(
                array(
                    'street' => array('4 Jersey St', 'multiple-error-messages'),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US'
                )
            );
            self::$shipengine->normalizeAddress($validate_with_error);
        } catch (BusinessRuleException $e) {
            $error = $e->jsonSerialize();
            self::assertInstanceOf(BusinessRuleException::class, $e);
            self::assertNotEmpty($error['request_id']);
            self::assertStringStartsWith('req_', $error['request_id']);
            self::assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            self::assertEquals(ErrorType::BUSINESS_RULES, $error['type']);
            self::assertEquals(ErrorCode::INVALID_ADDRESS, $error['error_code']);
            self::assertEquals(
                "Invalid address.\nInvalid City, State, or Zip\nInvalid postal code",
                $error['message']
            );
        }
    }

    public function testJsonSerialize(): void
    {
        $good_address = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        self::assertIsArray(self::$shipengine->validateAddress($good_address)->jsonSerialize());
    }

    public function addressObjectAssertions($object)
    {
        self::assertInstanceOf(Address::class, $object);
        self::assertObjectHasAttribute('street', $object);
        self::assertObjectHasAttribute('city_locality', $object);
        self::assertObjectHasAttribute('state_province', $object);
        self::assertObjectHasAttribute('postal_code', $object);
        self::assertObjectHasAttribute('country_code', $object);
        self::assertObjectHasAttribute('name', $object);
        self::assertObjectHasAttribute('phone', $object);
        self::assertObjectHasAttribute('company', $object);
    }

    public function addressValidateResultAssertions($object): void
    {
        self::assertInstanceOf(AddressValidateResult::class, $object);
        self::assertObjectHasAttribute('valid', $object);
        self::assertObjectHasAttribute('normalized_address', $object);
        self::assertObjectHasAttribute('info', $object);
        self::assertObjectHasAttribute('warnings', $object);
        self::assertObjectHasAttribute('errors', $object);
    }
}
