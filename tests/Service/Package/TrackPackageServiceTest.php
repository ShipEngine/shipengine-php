<?php declare(strict_types=1);

namespace Service\Package;

use DateInterval;
use PHPUnit\Framework\TestCase;
use ShipEngine\Message\BusinessRuleException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Model\Package\TrackingQuery;
use ShipEngine\Model\Package\TrackPackageResult;
use ShipEngine\ShipEngine;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorSource;
use ShipEngine\Util\Constants\ErrorType;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineConfig
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\Service\Package\TrackPackageService
 * @covers \ShipEngine\Message\Events\RequestSentEvent
 * @covers \ShipEngine\Message\Events\ResponseReceivedEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEventListener
 * @covers \ShipEngine\Model\Carriers\Carrier
 * @covers \ShipEngine\Model\Carriers\CarrierAccount
 * @covers \ShipEngine\Model\Package\Package
 * @covers \ShipEngine\Model\Package\Shipment
 * @covers \ShipEngine\Model\Package\TrackPackageResult
 * @covers \ShipEngine\Model\Package\TrackingEvent
 * @covers \ShipEngine\Model\Package\TrackingQuery
 * @covers \ShipEngine\Service\Carriers\CarrierAccountService
 * @covers \ShipEngine\Util\Assert
 * @covers \ShipEngine\Util\IsoString
 * @covers \ShipEngine\Util\VersionInfo
 * @covers \ShipEngine\Model\Package\Location
 * @uses \ShipEngine\Message\ShipEngineException
 */
final class TrackPackageServiceTest extends TestCase
{
    private static ShipEngine $shipengine;

    public static function setUpBeforeClass(): void
    {
        self::$shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15000S')
            )
        );
    }

    public function testInvalidTrackingNumber(): void
    {
        $trackingData = new TrackingQuery(
            'fedex',
            'abc123'
        );

        try {
            self::$shipengine->trackPackage($trackingData);
        } catch (ShipEngineException $err) {
            $error = $err->jsonSerialize();
            $trackingNumber = $trackingData->trackingNumber;
            $this->assertInstanceOf(SystemException::class, $err);
            $this->assertNotNull($error['requestId']);
            $this->assertStringStartsWith('req_', $error['requestId']);
            $this->assertEquals(ErrorSource::CARRIER, $error['source']);
            $this->assertEquals(ErrorType::BUSINESS_RULES, $error['type']);
            $this->assertEquals(ErrorCode::INVALID_IDENTIFIER, $error['errorCode']);
            $this->assertEquals(
                "$trackingNumber is not a valid fedex tracking number.",
                $error['message']
            );
        }
    }

    public function testInvalidPackageId(): void
    {
        $packageId = 'pkg_12!@3a s567';

        try {
            self::$shipengine->trackPackage($packageId);
        } catch (ShipEngineException $err) {
            $error = $err->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $err);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::INVALID_IDENTIFIER, $error['errorCode']);
            $this->assertEquals(
                "[$packageId] is not a valid package ID.",
                $error['message']
            );
        }
    }

    public function testPackageIdNotFound(): void
    {
        $packageId = 'pkg_123';

        try {
            self::$shipengine->trackPackage($packageId);
        } catch (ShipEngineException $err) {
            $error = $err->jsonSerialize();
            $this->assertInstanceOf(SystemException::class, $err);
            $this->assertNotNull($error['requestId']);
            $this->assertStringStartsWith('req_', $error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::INVALID_IDENTIFIER, $error['errorCode']);
            $this->assertEquals(
                "Package ID $packageId does not exist.",
                $error['message']
            );
        }
    }

    public function testInvalidPackageIdPrefix(): void
    {
        $packageId = 'car_1FedExAccepted';
        $subString = substr($packageId, 0, 4);

        try {
            self::$shipengine->trackPackage($packageId);
        } catch (ShipEngineException $err) {
            $error = $err->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $err);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::INVALID_IDENTIFIER, $error['errorCode']);
            $this->assertEquals(
                "[$subString] is not a valid package ID prefix.",
                $error['message']
            );
        }
    }

    public function testTrackByTrackingNumberAndCarrierCode(): void
    {
        $trackingData = new TrackingQuery(
            'fedex',
            'abcFedExDelivered'
        );
        $trackingResult = self::$shipengine->trackPackage($trackingData);

        $this->assertEquals($trackingData->carrierCode, $trackingResult->shipment->carrier->code);
        $this->assertEquals($trackingData->trackingNumber, $trackingResult->package->trackingNumber);
        $this->assertNotNull($trackingResult->package->trackingUrl);
        $this->assertIsString($trackingResult->package->trackingUrl);
    }

    public function testTrackByPackageId(): void
    {
        $packageId = 'pkg_1FedExAccepted';
        $trackingResult = self::$shipengine->trackPackage($packageId);

        $this->assertEquals($packageId, $trackingResult->package->packageId);
        $this->assertNotEmpty($trackingResult->shipment->shipmentId);
        $this->assertNotNull($trackingResult->shipment->shipmentId);
        $this->assertNotEmpty($trackingResult->shipment->accountId);
        $this->assertNotNull($trackingResult->shipment->accountId);
        $this->assertNotEmpty($trackingResult->package->trackingNumber);
        $this->assertNotNull($trackingResult->package->trackingNumber);
    }

    public function testInitialScanTrackingEvent(): void
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_1FedExAccepted');
        $this->trackPackageAssertions($trackingResult);
        $this->assertArrayHasKey(0, $trackingResult->events);
        $this->assertArrayNotHasKey(1, $trackingResult->events);
        $this->assertCount(1, $trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
    }

    public function testOutForDeliveryTrackingEvent(): void
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_1FedExAttempted');
        $this->trackPackageAssertions($trackingResult);
        $this->assertArrayHasKey(0, $trackingResult->events);
        $this->assertArrayHasKey(1, $trackingResult->events);
        $this->assertCount(5, $trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('in_transit', $trackingResult->events[1]->status);
    }

    public function testDevliveredFirstTryTrackingEvent(): void
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_1FedExDeLivered');
        $this->trackPackageAssertions($trackingResult);
        $this->assertEquals($trackingResult->shipment->actualDeliveryDate, $trackingResult->events[4]->dateTime);
        $this->doesDeliveryDateMatch($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('in_transit', $trackingResult->events[1]->status);
        $this->assertEquals('delivered', $trackingResult->events[4]->status);
        $this->assertEquals(
            'delivered',
            $trackingResult->events[array_key_last($trackingResult->events)]->status
        );
    }

    public function testMultipleDeliveryAttemptEvents(): void
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_1FedexDeLiveredAttempted');
        $this->trackPackageAssertions($trackingResult);
        $this->assertCount(9, $trackingResult->events);
        $this->assertEventsInOrder($trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('in_transit', $trackingResult->events[1]->status);
        $this->assertEquals('unknown', $trackingResult->events[2]->status);
        $this->assertEquals('in_transit', $trackingResult->events[3]->status);
        $this->assertEquals('attempted_delivery', $trackingResult->events[4]->status);
        $this->assertEquals('in_transit', $trackingResult->events[5]->status);
        $this->assertEquals('attempted_delivery', $trackingResult->events[6]->status);
        $this->assertEquals('in_transit', $trackingResult->events[7]->status);
        $this->assertEquals('delivered', $trackingResult->events[8]->status);
    }

    public function testDeliveryWithSignuatureTrackingEvent()
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_1FedexDeLivered');
        $this->trackPackageAssertions($trackingResult);
        $this->assertCount(5, $trackingResult->events);
        $this->assertEventsInOrder($trackingResult->events);
        $this->doesDeliveryDateMatch($trackingResult);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('in_transit', $trackingResult->events[1]->status);
        $this->assertEquals('unknown', $trackingResult->events[2]->status);
        $this->assertEquals('in_transit', $trackingResult->events[3]->status);
        $this->assertEquals('delivered', $trackingResult->events[4]->status);
        $this->assertNotNull($trackingResult->events[4]->signer);
        $this->assertNotEmpty($trackingResult->events[4]->signer);
        $this->assertIsString($trackingResult->events[4]->signer);
    }

    public function testDeliveredAfterMultipleAttempts()
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_1FedexDeLiveredAttempted');

        $this->trackPackageAssertions($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->doesDeliveryDateMatch($trackingResult);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('in_transit', $trackingResult->events[1]->status);
        $this->assertEquals('attempted_delivery', $trackingResult->events[4]->status);
        $this->assertEquals('attempted_delivery', $trackingResult->events[6]->status);
        $this->assertEquals('delivered', $trackingResult->events[8]->status);
        $this->assertEquals(
            'delivered',
            $trackingResult->events[array_key_last($trackingResult->events)]->status
        );
    }

    public function testDeliveredAfterExceptionTrackingEvent()
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_1FedexDeLiveredException');

        $this->trackPackageAssertions($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->doesDeliveryDateMatch($trackingResult);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('in_transit', $trackingResult->events[1]->status);
        $this->assertEquals('exception', $trackingResult->events[4]->status);
        $this->assertEquals(
            'delivered',
            $trackingResult->events[array_key_last($trackingResult->events)]->status
        );
    }

    public function testSignleExceptionTrackingEvent()
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_1FedexException');

        $this->trackPackageAssertions($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->assertCount(3, $trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('in_transit', $trackingResult->events[1]->status);
        $this->assertEquals('exception', $trackingResult->events[2]->status);
    }

    public function testServerSideError(): void
    {
        $trackingData = new TrackingQuery(
            'fedex',
            '500 Server Error'
        );

        try {
            self::$shipengine->trackPackage($trackingData);
        } catch (ShipEngineException $err) {
            $error = $err->jsonSerialize();
            $this->assertInstanceOf(SystemException::class, $err);
            $this->assertNotEmpty($error['requestId']);
            $this->assertStringStartsWith('req_', $error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::SYSTEM, $error['type']);
            $this->assertEquals(ErrorCode::UNSPECIFIED, $error['errorCode']);
            $this->assertEquals(
                "Unable to connect to the database",
                $error['message']
            );
        }
    }

    public function testMultipleExcpetionsInTrackingEvents()
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_DeLiveredException');

        $this->trackPackageAssertions($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->assertCount(8, $trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('exception', $trackingResult->events[4]->status);
        $this->assertEquals('exception', $trackingResult->events[5]->status);
    }

    public function testMultipleLocationsInTrackingEvent()
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_Attempted');

//        print_r($trackingResult->events[2]);

        $this->trackPackageAssertions($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->assertNull($trackingResult->events[0]->location);
        $this->assertNull($trackingResult->events[4]->location->latitude);
        $this->assertNull($trackingResult->events[4]->location->longitude);
        $this->assertNotNull($trackingResult->events[2]->location->latitude);
        $this->assertNotNull($trackingResult->events[2]->location->longitude);
    }

    public function trackPackageAssertions(TrackPackageResult $trackingResult): void
    {
        $carrierAccountCarrierCode = $trackingResult->shipment->carrierAccount->carrier->code;
        $carrierCode = $trackingResult->shipment->carrier->code;
        $estimatedDelivery = $trackingResult->shipment->estimatedDeliveryDate;
        $this->assertNotNull($carrierAccountCarrierCode);
        $this->assertNotEmpty($carrierAccountCarrierCode);
        $this->assertIsString($carrierAccountCarrierCode);
        $this->assertNotNull($carrierCode);
        $this->assertNotEmpty($carrierCode);
        $this->assertIsString($carrierCode);
        $this->assertNotNull($estimatedDelivery);
        $this->assertNotEmpty($estimatedDelivery);
        $this->assertTrue($estimatedDelivery->hasTime());
        $this->assertTrue($estimatedDelivery->hasTimezone());
    }

    public function doesDeliveryDateMatch(TrackPackageResult $trackingResult)
    {
        $this->assertEquals(
            $trackingResult->shipment->actualDeliveryDate,
            $trackingResult->events[array_key_last($trackingResult->events)]->dateTime
        );
    }

    public function assertEventsInOrder(array $events)
    {
        $previousDateTime = $events[0]->dateTime;
        foreach ($events as $event) {
            $status = $event->status;
            assert(
                $event->dateTime >= $previousDateTime,
                "Event $status has an earlier timestamp than $previousDateTime."
            );

            $previousDateTime = $event->dateTime;
        }
    }
}
