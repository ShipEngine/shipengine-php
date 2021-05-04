<?php declare(strict_types=1);

namespace Model\Carriers;

use PHPUnit\Framework\TestCase;
use ShipEngine\Message\InvalidFieldValueException;
use ShipEngine\Model\Carriers\Carrier;
use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\Util\Constants\Carriers;

/**
 * @covers \ShipEngine\Model\Carriers\CarrierAccount
 * @covers \ShipEngine\Message\InvalidFieldValueException
 * @covers \ShipEngine\Message\ShipEngineException
 * @covers \ShipEngine\Model\Carriers\Carrier
 */
final class CarrierAccountTest extends TestCase
{
    /**
     * Test instantiation of the CarrierAccount class.
     */
    public function testCarrierAccountInstantiation(): void
    {
        $carrier_code = new CarrierAccount(
            array(
                'carrier_code' => Carriers::FEDEX,
                'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'account_number' => 'SDF987',
                'name' => 'ShipEngine FedEx Account',
            )
        );
        $this->assertInstanceOf(CarrierAccount::class, $carrier_code);
    }

    public function testEachSDKSupportedCarrier()
    {
        $accounts = array(
            array(
                'carrier_code' => Carriers::FEDEX,
                'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'account_number' => 'SDF987',
                'name' => 'ShipEngine FedEx Account',
            ),
            array(
                'carrier_code' => Carriers::UPS,
                'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'account_number' => 'SDF987',
                'name' => 'ShipEngine UPS Account',
            ),
            array(
                'carrier_code' => Carriers::USPS,
                'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'account_number' => 'SDF987',
                'name' => 'ShipEngine USPS Account',
            )
        );

        $account_iterator = function (array $carrier_codes) {
            $arr = [];
            foreach ($carrier_codes as $account) {
                $acct = new CarrierAccount($account);
                $arr[] = $acct;
            }
            return $arr;
        };
        $carrier_codes = $account_iterator($accounts);

//        print_r($carrier_codes);
        $this->assertInstanceOf(CarrierAccount::class, $carrier_codes[0]);
        $this->assertInstanceOf(Carrier::class, $carrier_codes[0]->carrier_account);

        $this->assertInstanceOf(CarrierAccount::class, $carrier_codes[1]);
        $this->assertInstanceOf(Carrier::class, $carrier_codes[1]->carrier_account);

        $this->assertInstanceOf(CarrierAccount::class, $carrier_codes[2]);
        $this->assertInstanceOf(Carrier::class, $carrier_codes[2]->carrier_account);
    }

    public function testInvalidCarrierAccountValue(): void
    {
        $invalid_carrier_code = array(
            'carrier_code' => 'canada_post',
            'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
            'account_number' => 'SDF987',
            'name' => 'Canada Post',
        );
        $account = $invalid_carrier_code['carrier_code'];
        try {
            new CarrierAccount($invalid_carrier_code);
        } catch (InvalidFieldValueException $err) {
            $this->assertInstanceOf(InvalidFieldValueException::class, $err);
            $this->assertEquals('carrier_account', $err->field_name);
            $this->assertEquals('invalid_field_value', $err->error_code);
            $this->assertEquals(
                "carrier_account - Carrier [$account] is currently not supported.",
                $err->getMessage()
            );
        }
    }

    public function testJsonSerialize(): void
    {
        $ups_account_info = array(
            'carrier_code' => Carriers::UPS,
            'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
            'account_number' => 'SDF987',
            'name' => 'ShipEngine UPS Account',
        );
        $ups_carrier_code = new CarrierAccount($ups_account_info);
        $json_serialize = $ups_carrier_code->jsonSerialize();
        $encoded_json = json_encode($json_serialize);
        $this->assertJson($encoded_json);
        $this->assertIsString($encoded_json);
        $this->assertIsArray($json_serialize);
    }
}
