<?php declare(strict_types=1);

namespace Model\Carriers;

use ShipEngine\Message\InvalidFieldValueException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Carriers\CarrierAccount;
use PHPUnit\Framework\TestCase;
use ShipEngine\Service\Carriers\FedEx;
use ShipEngine\Service\Carriers\UPS;
use ShipEngine\Service\Carriers\USPS;

/**
 * @covers \ShipEngine\Model\Carriers\CarrierAccount
 * @covers \ShipEngine\Message\InvalidFieldValueException
 * @covers \ShipEngine\Message\ShipEngineException
 * @covers \ShipEngine\Service\Carriers\UPS
 */
final class CarrierAccountTest extends TestCase
{
    /**
     * Test instantiation of the CarrierAccount class.
     */
    public function testCarrierAccountInstantiation(): void
    {
        $carrier_account = new CarrierAccount(
            array(
                'carrier_account' => 'fedex',
                'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'account_number' => 'SDF987',
                'account_name' => 'ShipEngine FedEx Account',
            )
        );
        $this->assertInstanceOf(CarrierAccount::class, $carrier_account);
    }

    public function testEachSDKSupportedCarrier(): void
    {
        $accounts = array(
            array(
                'carrier_account' => 'fedex',
                'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'account_number' => 'SDF987',
                'account_name' => 'ShipEngine FedEx Account',
            ),
            array(
                'carrier_account' => 'ups',
                'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'account_number' => 'SDF987',
                'account_name' => 'ShipEngine UPS Account',
            ),
            array(
                'carrier_account' => 'stamps_com',
                'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'account_number' => 'SDF987',
                'account_name' => 'ShipEngine USPS Account',
            )
        );

        $account_iterator = function (array $carrier_accounts) {
            $arr = [];
            foreach ($carrier_accounts as $account) {
                $acct = new CarrierAccount($account);
                array_push($arr, $acct);
            }
            return $arr;
        };
        $carrier_accounts = $account_iterator($accounts);

        $this->assertInstanceOf(CarrierAccount::class, $carrier_accounts[0]);
        $this->assertInstanceOf(FedEx::class, $carrier_accounts[0]->carrier_account);

        $this->assertInstanceOf(CarrierAccount::class, $carrier_accounts[1]);
        $this->assertInstanceOf(UPS::class, $carrier_accounts[1]->carrier_account);

        $this->assertInstanceOf(CarrierAccount::class, $carrier_accounts[2]);
        $this->assertInstanceOf(USPS::class, $carrier_accounts[2]->carrier_account);
    }

    public function testInvalidCarrierAccountValue(): void
    {
        $invalid_carrier_account = array(
            'carrier_account' => 'canada_post',
            'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
            'account_number' => 'SDF987',
            'account_name' => 'Canada Post',
        );
        $account = $invalid_carrier_account['carrier_account'];
        $field_value = key($invalid_carrier_account);
        try {
            new CarrierAccount($invalid_carrier_account);
        } catch (InvalidFieldValueException $err) {
            $this->assertInstanceOf(InvalidFieldValueException::class, $err);
            $this->assertEquals('carrier_account', $err->field_name);
            $this->assertEquals('invalid_field_value', $err->error_code);
            $this->assertEquals(
                "$field_value - Carrier [$account] is currently not supported.",
                $err->getMessage()
            );
        }
    }

    public function testJsonSerialize(): void
    {
        $ups_account_info = array(
            'carrier_account' => 'ups',
            'id' => 'car_a09a8jsfd09wjzxcs9dfyha',
            'account_number' => 'SDF987',
            'account_name' => 'ShipEngine UPS Account',
        );
        $ups_carrier_account = new CarrierAccount($ups_account_info);
        $json_serialize = $ups_carrier_account->jsonSerialize();
        $encoded_json = json_encode($json_serialize);
        $this->assertJson($encoded_json);
        $this->assertIsString($encoded_json);
        $this->assertIsArray($json_serialize);
    }
}
