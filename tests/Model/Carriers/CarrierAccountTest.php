<?php declare(strict_types=1);

namespace Model\Carriers;

use PHPUnit\Framework\TestCase;
use ShipEngine\Message\InvalidFieldValueException;
use ShipEngine\Model\Carriers\Carrier;
use ShipEngine\Model\Carriers\CarrierAccount;
use ShipEngine\Util\Constants\Carriers;

/**
 * @covers \ShipEngine\Model\Carriers\CarrierAccount
 * @uses   \ShipEngine\Message\InvalidFieldValueException
 * @uses   \ShipEngine\Message\ShipEngineException
 * @uses   \ShipEngine\Model\Carriers\Carrier
 * @uses   \ShipEngine\Util\Constants\CarrierNames
 * @uses   \ShipEngine\Util\Constants\Carriers
 */
final class CarrierAccountTest extends TestCase
{
    /**
     * Test instantiation of the CarrierAccount class.
     */
    public function testCarrierAccountInstantiation(): void
    {
        $carrierCode = new CarrierAccount(
            array(
                'carrierCode' => Carriers::FEDEX,
                'accountId' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'accountNumber' => 'SDF987',
                'name' => 'ShipEngine FedEx Account',
            )
        );
        $this->assertInstanceOf(CarrierAccount::class, $carrierCode);
    }

    public function testEachSDKSupportedCarrier(): void
    {
        $accounts = array(
            array(
                'carrierCode' => Carriers::FEDEX,
                'accountId' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'accountNumber' => 'SDF987',
                'name' => 'ShipEngine FedEx Account',
            ),
            array(
                'carrierCode' => Carriers::UPS,
                'accountId' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'accountNumber' => 'SDF987',
                'name' => 'ShipEngine UPS Account',
            ),
            array(
                'carrierCode' => Carriers::USPS,
                'accountId' => 'car_a09a8jsfd09wjzxcs9dfyha',
                'accountNumber' => 'SDF987',
                'name' => 'ShipEngine USPS Account',
            )
        );

        $account_iterator = static function (array $carrierCodes) {
            $arr = [];
            foreach ($carrierCodes as $account) {
                $acct = new CarrierAccount($account);
                $arr[] = $acct;
            }
            return $arr;
        };
        $carrierCodes = $account_iterator($accounts);

        $this->assertInstanceOf(CarrierAccount::class, $carrierCodes[0]);
        $this->assertInstanceOf(Carrier::class, $carrierCodes[0]->carrier);

        $this->assertInstanceOf(CarrierAccount::class, $carrierCodes[1]);
        $this->assertInstanceOf(Carrier::class, $carrierCodes[1]->carrier);

        $this->assertInstanceOf(CarrierAccount::class, $carrierCodes[2]);
        $this->assertInstanceOf(Carrier::class, $carrierCodes[2]->carrier);
    }

    public function testInvalidCarrierAccountValue(): void
    {
        $invalidCarrierAccount = array(
            'carrierCode' => 'united_post',
            'accountId' => 'car_a09a8jsfd09wjzxcs9dfyha',
            'accountNumber' => 'SDF987',
            'name' => 'Canada Post',
        );
        $account = $invalidCarrierAccount['carrierCode'];
        try {
            new CarrierAccount($invalidCarrierAccount);
        } catch (InvalidFieldValueException $err) {
            $this->assertInstanceOf(InvalidFieldValueException::class, $err);
            $this->assertEquals('carrier', $err->field_name);
            $this->assertEquals('invalid_field_value', $err->errorCode);
            $this->assertEquals(
                "carrier - Carrier [$account] is currently not supported.",
                $err->getMessage()
            );
        }
    }

    public function testJsonSerialize(): void
    {
        $ups_account_info = array(
            'carrierCode' => Carriers::UPS,
            'accountId' => 'car_a09a8jsfd09wjzxcs9dfyha',
            'accountNumber' => 'SDF987',
            'name' => 'ShipEngine UPS Account',
        );
        $ups_carrierCode = new CarrierAccount($ups_account_info);
        $json_serialize = $ups_carrierCode->jsonSerialize();
        $encoded_json = json_encode($json_serialize);
        $this->assertJson($encoded_json);
        $this->assertIsString($encoded_json);
        $this->assertIsArray($json_serialize);
    }
}
