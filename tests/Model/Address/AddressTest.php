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
                    'Apt 32-B',
                ),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
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
     * - **requestId** is `null`.
     * - **source** is `ShipEngine`.
     * - **type** is `validation`.
     * - **errorCode** os `field_value_required`.
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
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['errorCode']);
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
     * - **errorCode** os `field_value_required`.
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
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['errorCode']);
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
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['errorCode']);
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
                    'cityLocality' => '',
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
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['errorCode']);
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
     * - **errorCode** os `field_value_required`.
     * - **message** is -
     * "Invalid address. Either the postal code or the city/locality and state/province must be specified.".
     */
    public function testMissingStateAndPostalCode(): void
    {
        try {
            new Address(
                array(
                    'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
                    'cityLocality' => 'Boston',
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
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['errorCode']);
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
     * - **errorCode** os `invalid_field_value`.
     * - **message** is "Invalid address. The countryCode must be specified.".
     */
    public function testMissingcountryCode(): void
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
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['errorCode']);
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
     * - **errorCode** os `invalid_field_value`.
     * - **message** is "Invalid address. XX is not a valid countryCode code."
     * (where XX is the value that was specified).
     */
    public function testInvalidcountryCode(): void
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
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['errorCode']);
            $this->assertEquals(
                "Invalid address. USA is not a valid countryCode code.",
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
