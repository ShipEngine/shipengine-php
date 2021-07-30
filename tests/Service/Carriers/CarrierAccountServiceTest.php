<?php declare(strict_types=1);

// namespace Service\Carriers;

// use DateInterval;
// use PHPUnit\Framework\TestCase;
// use ShipEngine\Message\ShipEngineException;
// use ShipEngine\Message\SystemException;
// use ShipEngine\Model\Carriers\CarrierAccount;
// use ShipEngine\ShipEngine;
// use ShipEngine\Util\Constants\Endpoints;
// use ShipEngine\Util\Constants\ErrorCode;
// use ShipEngine\Util\Constants\ErrorSource;
// use ShipEngine\Util\Constants\ErrorType;

// *
//  * @covers \ShipEngine\Util\Assert
//  * @covers \ShipEngine\Message\ShipEngineException
//  * @covers \ShipEngine\Service\Carriers\CarrierAccountService
//  * @covers \ShipEngine\ShipEngineConfig
//  * @covers \ShipEngine\ShipEngineClient
//  * @covers \ShipEngine\ShipEngine
//  * @covers \ShipEngine\Model\Carriers\Carrier
//  * @covers \ShipEngine\Model\Carriers\CarrierAccount
//  * @covers \ShipEngine\Util\Constants\Carriers
//  * @covers \ShipEngine\Util\Constants\CarrierNames
//  * @covers \ShipEngine\Message\Events\ShipEngineEvent
//  * @covers \ShipEngine\Message\Events\ShipEngineEventListener
//  * @covers \ShipEngine\Message\Events\RequestSentEvent
//  * @covers \ShipEngine\Message\Events\ResponseReceivedEvent
//  * @uses   \ShipEngine\Message\Events\EventMessage
//  * @uses   \ShipEngine\Message\Events\EventOptions
 
// final class CarrierAccountServiceTest extends TestCase
// {
//     private static ShipEngine $shipengine;

//     public static function setUpBeforeClass(): void
//     {
//         self::$shipengine = new ShipEngine(
//             array(
//                 'apiKey' => 'TEST_ycvJAgX6tLB1Awm9WGJmD8mpZ8wXiQ20WhqFowCk32s',
//                 'baseUrl' => Endpoints::TEST_REST_URL,
//                 'pageSize' => 75,
//                 'retries' => 1,
//                 'timeout' => new DateInterval('PT15S')
//             )
//         );
//     }

//     public function testFetchCarrierAccountsReturnValue(): void
//     {
//         $carrier_accounts = self::$shipengine->listCarriers();

//         foreach ($carrier_accounts as $account) {
//             $this->assertInstanceOf(CarrierAccount::class, $account);
//             $this->assertStringStartsWith('car_', $account->accountId);
//         }
//     }

    // public function testFetchWithMultipleAccounts(): void
    // {
    //     $carrier_accounts = self::$shipengine->listCarriers();

    //     $this->assertIsArray($carrier_accounts);
    //     $this->assertArrayHasKey(0, $carrier_accounts);
    //     $this->assertArrayHasKey(1, $carrier_accounts);
    //     $this->assertArrayHasKey(2, $carrier_accounts);
    //     $this->assertNotEquals($carrier_accounts[0]->accountId, $carrier_accounts[1]->accountId);
    //     $this->assertNotEquals($carrier_accounts[1]->accountId, $carrier_accounts[3]->accountId);
    //     $this->assertNotEquals($carrier_accounts[2]->accountId, $carrier_accounts[3]->accountId);
    //     $this->assertEquals('ups', $carrier_accounts[0]->carrier->code);
    //     $this->assertEquals('usps', $carrier_accounts[3]->carrier->code);
    //     $this->assertEquals('fedex', $carrier_accounts[1]->carrier->code);
    //     foreach ($carrier_accounts as $account) {
    //         $this->assertInstanceOf(CarrierAccount::class, $account);
    //         $this->assertStringStartsWith('car_', $account->accountId);
    //         $this->assertObjectHasAttribute('name', $account);
    //     }
    // }

    // public function testFetchWithMultipleAccountsOfSameCarrier(): void
    // {
    //     $carrier_accounts = self::$shipengine->listCarriers();

    //     $this->assertArrayHasKey(0, $carrier_accounts);
    //     $this->assertArrayHasKey(1, $carrier_accounts);
    //     $this->assertArrayHasKey(2, $carrier_accounts);
    //     $this->assertArrayHasKey(3, $carrier_accounts);
    //     $this->assertEquals('ups', $carrier_accounts[0]->carrier->code);
    //     $this->assertEquals('fedex', $carrier_accounts[1]->carrier->code);
    //     $this->assertEquals('fedex', $carrier_accounts[2]->carrier->code);
    //     $this->assertNotEquals($carrier_accounts[0]->name, $carrier_accounts[1]->name);
    //     foreach ($carrier_accounts as $account) {
    //         $this->assertInstanceOf(CarrierAccount::class, $account);
    //         $this->assertStringStartsWith('car_', $account->accountId);
    //         $this->assertObjectHasAttribute('name', $account);
    //     }
    // }

    // public function testNoCarrierAccountsSetup(): void
    // {
    //     $carrier_accounts = self::$shipengine->listCarriers('sendle');

    //     $this->assertIsArray($carrier_accounts);
    //     $this->assertCount(0, $carrier_accounts);
    //     $this->assertEmpty($carrier_accounts);
    //     $this->assertNotInstanceOf(ShipEngineException::class, $carrier_accounts);
    // }

    // public function testServerSideError(): void
    // {
    //     try {
    //         self::$shipengine->listCarriers('access_worldwide');
    //     } catch (SystemException $e) {
    //         $error = $e->jsonSerialize();
    //         $this->assertInstanceOf(SystemException::class, $e);
    //         $this->assertNotEmpty($error['requestId']);
    //         $this->assertStringStartsWith('req_', $error['requestId']);
    //         $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
    //         $this->assertEquals(ErrorType::SYSTEM, $error['type']);
    //         $this->assertEquals(ErrorCode::UNSPECIFIED, $error['errorCode']);
    //         $this->assertEquals(
    //             "Unable to connect to the database",
    //             $error['message']
    //         );
    //     }
    // }
// }
