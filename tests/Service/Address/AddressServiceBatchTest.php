<?php declare(strict_types=1);

namespace Service\Address;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Address\AddressResult;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\ShipEngine;

/**
 * Tests the batched method provided in the `AddressService` that allows for validation multiple addresses.
 *
 * @covers \ShipEngine\Model\Address\AddressResult
 * @covers \ShipEngine\Service\Address\AddressService
 * @covers \ShipEngine\Model\Address\AddressValidateResult
 * @covers \ShipEngine\Service\AbstractService
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 */
final class AddressServiceBatchTest extends TestCase
{
    private static ShipEngine $shipengine;

    private static array $batchAddresses;

    private static array $batchAddressResponse;

    /**
     * Pass an `api-key` into the new instance of the *ShipEngine* class and instantiate fixtures.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        putenv('CLIENT_BASE_URI=https://simengine.herokuapp.com');
        self::$shipengine = new ShipEngine('baz');
        self::$batchAddresses = array(
            0 =>
                array(
                    'street' =>
                        array(
                            '4 Jersey St',
                            'ste 200'
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
                            '4 Jersey St',
                            'ste 200'
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
//        print_r(json_encode(self::$batchAddresses, JSON_PRETTY_PRINT));
        $batchValidation = self::$shipengine->validateAddresses(self::$batchAddresses);

        $this->assertInstanceOf(AddressValidateResult::class, $batchValidation[0]);
        $this->assertInstanceOf(AddressValidateResult::class, $batchValidation[1]);
    }
}
