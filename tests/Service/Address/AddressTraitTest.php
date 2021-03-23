<?php declare(strict_types=1);

namespace Service\Address;

use PHPUnit\Framework\TestCase;
use ShipEngine\Message\ShipEngineError;
use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;

/**
 * Tests the methods provided in the `AddressTrait`.
 *
 * @covers \ShipEngine\Model\Address\Address
 * @covers \ShipEngine\Service\Address\AddressTrait
 * @covers \ShipEngine\Service\Address\AddressService
 * @covers \ShipEngine\Service\AbstractService
 * @covers \ShipEngine\Service\ServiceFactory
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 */
final class AddressTraitTest extends TestCase
{
    /**
     * @var ShipEngine
     */
    private static ShipEngine $shipengine;

    /**
     * @var array|string[]
     */
    private static array $street;

    /**
     * @var string
     */
    private static string $city;

    /**
     * @var string
     */
    private static string $state;

    /**
     * @var string
     */
    private static string $postal_code;

    /**
     * @var string
     */
    private static string $country_code;

    /**
     * Pass in an `api-key` the new instance of the *ShipEngine* class and create fixtures.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$street = array(
           '4 Jersey St',
           'Ste 200',
        );
        self::$city = 'Boston';
        self::$state = 'MA';
        self::$postal_code = '02215';
        self::$country_code = 'US';
        self::$shipengine = new ShipEngine('baz');
    }

    /**
     * Test the return type, should be an instance of the `Address` Type.
     */
    public function testReturnType(): void
    {
        $validation = self::$shipengine->validateAddress(
            self::$street,
            self::$city,
            self::$state,
            self::$postal_code,
            self::$country_code
        );

        $this->assertInstanceOf(Address::class, $validation);
    }
}
