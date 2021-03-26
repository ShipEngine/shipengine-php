<?php declare(strict_types=1);

namespace Model\Address;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use ShipEngine\Message\ShipEngineValidationError;
use ShipEngine\Model\Address\AddressValidateParams;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * @covers \ShipEngine\Model\Address\AddressValidateParams;
 */
final class AddressValidateParamsTest extends TestCase
{
    private static ShipEngineSerializer $serializer;
    private static string $initial_address_validate_params;
    private static AddressValidateParams $successful_address_validate_params;

    public static function setUpBeforeClass(): void
    {
        self::$serializer = new ShipEngineSerializer();
        self::$initial_address_validate_params = json_encode(array(
            'street' =>
                array(
                    0 => 'validate-batch',
                ),
            'city_locality' => 'Boston',
            'state_province' => 'MA',
            'postal_code' => '02215',
            'country_code' => 'US',
        ));
        self::$successful_address_validate_params = self::$serializer->deserializeJsonToType(
            self::$initial_address_validate_params,
            AddressValidateParams::class
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
        $this->assertInstanceOf(AddressValidateParams::class, self::$successful_address_validate_params);
    }

    /**
     * Tests a validation with `error` messages.
     *
     * `Assertions:`
     * - **request_id** is `null`.
     * - **error_source** is `ShipEngine`.
     * - **error_type** is `validation`.
     * - **error_code** os `field_value_required`.
     * - **error_message** is "Invalid address. At least one address line is required.".
     */
    public function testNoAddressLinesValidationError()
    {
        try {
            new AddressValidateParams(
                array(),
                'Boston',
                'MA',
                '02215',
                'US',
            );
            $this->expectException(ShipEngineValidationError::class);
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('ShipEngine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals(
                'Invalid address. At least one address line is required.',
                $error['error_message']
            );
        }
    }

    public function testTooManyAddressLinesValidationError()
    {
        try {
            new AddressValidateParams(
                array('4 Jersey St', 'Ste 200', '2nd Floor', 'Clubhouse Level'),
                'Boston',
                'MA',
                '02215',
                'US',
            );
            $this->expectException(ShipEngineValidationError::class);
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('ShipEngine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals(
                'Invalid address. No more than 3 street lines are allowed.',
                $error['error_message']
            );
        }
    }

    public function testMissingCity()
    {
        try {
            new AddressValidateParams(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                '',
                'MA',
                '02215',
                'US',
            );
            $this->expectException(ShipEngineValidationError::class);
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('ShipEngine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                $error['error_message']
            );
        }
    }

    public function testMissingState()
    {
        try {
            new AddressValidateParams(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                'Boston',
                '',
                '02215',
                'US',
            );
            $this->expectException(ShipEngineValidationError::class);
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('ShipEngine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                $error['error_message']
            );
        }
    }

    public function testMissingPostalCode()
    {
        try {
            new AddressValidateParams(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                'Boston',
                'MA',
                '',
                'US',
            );
            $this->expectException(ShipEngineValidationError::class);
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('ShipEngine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                $error['error_message']
            );
        }
    }

    public function testMissingCountryCode()
    {
        try {
            new AddressValidateParams(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                'Boston',
                'MA',
                '02215',
                '',
            );
            $this->expectException(ShipEngineValidationError::class);
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('ShipEngine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals(
                'Invalid address. The country must be specified.',
                $error['error_message']
            );
        }
    }

    public function testInvalidCountryCode()
    {
        try {
            new AddressValidateParams(
                array('4 Jersey St', 'Ste 200', '2nd Floor'),
                'Boston',
                'MA',
                '02215',
                'USA',
            );
            $this->expectException(ShipEngineValidationError::class);
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('ShipEngine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals(
                "Invalid address. USA is not a valid country code.",
                $error['error_message']
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
