<?php declare(strict_types=1);

namespace Service\Address;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;

/**
 * Tests the batched method provided in the `AddressService` that allows for validation multiple addresses.
 *
 * @covers \ShipEngine\Model\Address\Address
 * @covers \ShipEngine\Service\Address\AddressTrait
 * @covers \ShipEngine\Service\Address\AddressService
 * @covers \ShipEngine\Model\Address\AddressValidateParams
 * @covers \ShipEngine\Model\Address\AddressValidateResult
 * @covers \ShipEngine\Service\AbstractService
 * @covers \ShipEngine\Service\ServiceFactory
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 */
final class AddressServiceBatchTest extends TestCase
{
    private static ShipEngine $shipengine;

    private static array $batchAddresses;

    private static array $batchAddressResponse;

    /**
     *
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$shipengine = new ShipEngine('baz');
        self::$batchAddresses = array(
            0 =>
                array(
                    'street' =>
                        array(
                            0 => 'validate-batch',
                        ),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                ),
            1 =>
                array(
                    'street' =>
                        array(
                            0 => 'validate-batch',
                        ),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                ),
        );
    }

    public function testValidateBatchMethodViaHTTP(): void
    {
        $batchValidation = self::$shipengine->addresses->validateAddresses(self::$batchAddresses);

        $this->assertInstanceOf(Address::class, $batchValidation[0]);
        $this->assertInstanceOf(Address::class, $batchValidation[1]);
    }
}
