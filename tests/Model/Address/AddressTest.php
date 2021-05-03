<?php declare(strict_types=1);

namespace Model\Address;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Model\Address\Address;
use ShipEngine\Util\ShipEngineSerializer;

/**
 *
 * @covers \ShipEngine\Message\Events\RequestSentEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEvent
 * @covers \ShipEngine\Model\Address\Address
 * @covers \ShipEngine\Util\Assert
 * @covers \ShipEngine\Message\ShipEngineException
 */
final class AddressTest extends TestCase
{
    private static Address $successful_address_validate_params;

    public static function setUpBeforeClass(): void
    {
        self::$successful_address_validate_params = new Address(
            array(
                'street' => array(
                    '4 Jersey St',
                    'validate-residential-address',
                ),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
    }

    /**
     * Test the instantiation via the construct function for the `AddressValidationParams` Type.
     *
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testConstruct(): void
    {
        $this->assertInstanceOf(Address::class, self::$successful_address_validate_params);
    }

    /**
     * Tests a validation with `error` messages.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **error_code** os `field_value_required`.
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
     * - **error_code** os `field_value_required`.
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

    public function testMissingCityStateAndPostalCode(): void
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

    public function testMissingCityAndPostalCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => '',
                    'state_province' => 'MA',
                    'postal_code' => '',
                    'country_code' => 'US',
                )
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
     * - **error_code** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testMissingStateAndPostalCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'city_locality' => 'Boston',
                    'state_province' => '',
                    'postal_code' => '',
                    'country_code' => 'US',
                )
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
     * - **error_code** os `invalid_field_value`.
     * - **message** is "Invalid address. The country must be specified.".
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
     * - **error_code** os `invalid_field_value`.
     * - **message** is "Invalid address. XX is not a valid country code."
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

    /**
     * Test the `jsonSerialize()` method on the *AddressValidationParams* type.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testJsonSerialize(): void
    {
        $json = self::$successful_address_validate_params->jsonSerialize();

        $this->assertNotNull($json);
        $this->assertIsArray($json);
    }
}
