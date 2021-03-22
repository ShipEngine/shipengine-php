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

    /**
     * Test the return type, should be an instance of the `Address` Type.
     */
    public function testReturnType(): void
    {
        $validation = $this->shipengine->validateAddress(
            $this->street,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country_code
        );

        $this->assertInstanceOf(Address::class, $validation);
    }
}
