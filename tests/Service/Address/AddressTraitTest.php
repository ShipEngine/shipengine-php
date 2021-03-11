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
    private ShipEngine $shipengine;

    /**
     * @var array|string[]
     */
    private array $street;

    /**
     * @var string
     */
    private string $city;

    /**
     * @var string
     */
    private string $state;

    /**
     * @var string
     */
    private string $postal_code;

    /**
     * @var string
     */
    private string $country_code;

    /**
     * Import `simengine/rpc/rpc.json` into *Hoverfly* before class instantiation.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        exec('hoverctl import simengine/rpc/rpc.json');
    }

    /**
     * Delete `simengine/rpc/rpc.json` from *Hoverfly*.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/rpc/rpc.json');
    }

    /**
     * Pass in an `api-key` the new instance of the *ShipEngine* class.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->street = array(
            '4 Jersey St',
            'ste 200'
        );
        $this->city = 'Boston';
        $this->state = 'MA';
        $this->postal_code = '02215';
        $this->country_code = 'US';

        $this->shipengine = new ShipEngine('baz');
    }

    public function testValidateAddress(): void
    {
        $validation = $this->shipengine->validateAddress(
            $this->street,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country_code
        );

        $this->assertEquals($this->city, $validation->city_locality);
    }

    /**
     * Test the return type, should be an instance of the `Address` Type.
     */
    public function testReturnType(): void {
        $validation = $this->shipengine->validateAddress(
            $this->street,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country_code
        );

        $this->assertInstanceOf(Address::class, $validation);
    }

    public function testValidateWithError(): void
    {
        $this->expectException(ShipEngineError::class);

        $this->shipengine->validateAddress(
            ['validate-with-error'],
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country_code
        );
    }
}
