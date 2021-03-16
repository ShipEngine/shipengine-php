<?php declare(strict_types=1);

namespace Service\Address;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Address\AddressValidateParams;
use ShipEngine\Model\Address\AddressValidateResult;
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
        exec('hoverctl import simengine/rpc/rpc.json');

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

    /**
     * Delete `simengine/rpc/rpc.json` from *Hoverfly*.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/rpc/rpc.json');
    }

    public function testValidateBatchMethodViaHTTP(): void
    {
        $batchValidation = self::$shipengine->addresses->validateAddresses(self::$batchAddresses);

        $this->assertInstanceOf(AddressValidateResult::class, $batchValidation[0]);
        $this->assertInstanceOf(AddressValidateResult::class, $batchValidation[1]);
    }
}
