<?php declare(strict_types=1);

namespace Model\Address;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
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
