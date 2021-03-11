<?php declare(strict_types=1);

namespace Model\Address;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use ShipEngine\Model\Address\AddressValidateParams;

/**
 * @covers \ShipEngine\Model\Address\AddressValidateParams;
 */
final class AddressValidateParamsTest extends TestCase
{
    /**
     * @var AddressValidateParams
     */
    private AddressValidateParams $address_validation_params;

    /**
     * Setup an instance of `AddressValidationParams`.
     */
    protected function setUp(): void
    {
        $this->address_validation_params = new AddressValidateParams(
            array('4 Jersey St', 'ste 200'),
            'US',
            'Boston',
            'MA',
            '02215'
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
        $this->assertInstanceOf(AddressValidateParams::class, $this->address_validation_params);
    }

    /**
     * Test the `jsonSerialize()` method on the *AddressValidationParams* type.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testJsonSerialize(): void
    {
        $json = $this->address_validation_params->jsonSerialize();

        $this->assertNotNull($json);
        $this->assertIsString($json);
    }

    /**
     * Test the instantiation via the construct function for the `AddressValidationParams` Type
     * with default *null values*.
     *
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testNullProperties(): void
    {
        $address_validation_params_with_null_values = new AddressValidateParams(
            array('4 Jersey St', 'ste 200'),
            'US'
        );

        $this->assertNull($address_validation_params_with_null_values->city_locality);
        $this->assertInstanceOf(AddressValidateParams::class, $address_validation_params_with_null_values);
    }

    public function testParse()
    {
       $this->assertEquals('Boston', $this->address_validation_params->city_locality);
    }
}
