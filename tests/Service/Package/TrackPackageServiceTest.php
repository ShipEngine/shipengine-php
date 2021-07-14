<?php declare(strict_types=1);

namespace Service\Package;

use DateInterval;
use PHPUnit\Framework\TestCase;
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
 * @covers   \ShipEngine\Service\Package\TrackPackageService
 * @covers   \ShipEngine\ShipEngine
 * @uses     \ShipEngine\ShipEngineConfig
 * @uses     \ShipEngine\ShipEngineClient
 * @uses     \ShipEngine\Message\Events\RequestSentEvent
 * @uses     \ShipEngine\Message\Events\ResponseReceivedEvent
 * @uses     \ShipEngine\Message\Events\ShipEngineEvent
 * @uses     \ShipEngine\Message\Events\ShipEngineEventListener
 * @uses     \ShipEngine\Model\Carriers\Carrier
 * @uses     \ShipEngine\Model\Carriers\CarrierAccount
 * @uses     \ShipEngine\Model\Package\Package
 * @uses     \ShipEngine\Model\Package\Shipment
 * @uses     \ShipEngine\Model\Package\TrackPackageResult
 * @uses     \ShipEngine\Model\Package\TrackingEvent
 * @uses     \ShipEngine\Model\Package\TrackingQuery
 * @uses     \ShipEngine\Service\Carriers\CarrierAccountService
 * @uses     \ShipEngine\Util\Assert
 * @uses     \ShipEngine\Util\IsoString
 * @uses     \ShipEngine\Model\Package\Location
 * @uses     \ShipEngine\Message\ShipEngineException
 * @uses     \ShipEngine\Message\Events\EventMessage
 * @uses     \ShipEngine\Message\Events\EventOptions
 * @uses     \ShipEngine\Util\Constants\CarrierNames
 * @uses     \ShipEngine\Util\Constants\Carriers
 */
final class TrackPackageServiceTest extends TestCase
{
    public function testInvalidTrackingNumber(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingData = new TrackingQuery(
            'fedex',
            'abc123'
        );

        try {
            $shipengine->trackPackage($trackingData);
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
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $packageId = 'pkg_12!@3a s567';

        try {
            $shipengine->trackPackage($packageId);
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
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $packageId = 'pkg_123';

        try {
            $shipengine->trackPackage($packageId);
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
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $packageId = 'car_1FedExAccepted';
        $subString = substr($packageId, 0, 4);

        try {
            $shipengine->trackPackage($packageId);
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
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingData = new TrackingQuery(
            'fedex',
            'abcFedExDelivered'
        );
        $trackingResult = $shipengine->trackPackage($trackingData);

        $this->assertEquals($trackingData->carrierCode, $trackingResult->shipment->carrier->code);
        $this->assertEquals($trackingData->trackingNumber, $trackingResult->package->trackingNumber);
        $this->assertNotNull($trackingResult->package->trackingUrl);
        $this->assertIsString($trackingResult->package->trackingUrl);
    }

    public function testTrackByPackageId(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $packageId = 'pkg_1FedExAccepted';
        $trackingResult = $shipengine->trackPackage($packageId);

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
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_1FedExAccepted');
        $this->trackPackageAssertions($trackingResult);
        $this->assertArrayHasKey(0, $trackingResult->events);
        $this->assertArrayNotHasKey(1, $trackingResult->events);
        $this->assertCount(1, $trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
    }

    public function testOutForDeliveryTrackingEvent(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_1FedExAttempted');
        $this->trackPackageAssertions($trackingResult);
        $this->assertArrayHasKey(0, $trackingResult->events);
        $this->assertArrayHasKey(1, $trackingResult->events);
        $this->assertCount(5, $trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('in_transit', $trackingResult->events[1]->status);
    }

    public function testDeliveredFirstTryTrackingEvent(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_1FedExDeLivered');
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
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_1FedexDeLiveredAttempted');
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

    public function testDeliveryWithSignatureTrackingEvent(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_1FedExDeLivered');
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

    public function testDeliveredAfterMultipleAttempts(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_1FedexDeLiveredAttempted');

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

    public function testDeliveredAfterExceptionTrackingEvent(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_1FedexDeLiveredException');

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

    public function testSingleExceptionTrackingEvent(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_1FedexException');

        $this->trackPackageAssertions($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->assertCount(3, $trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('in_transit', $trackingResult->events[1]->status);
        $this->assertEquals('exception', $trackingResult->events[2]->status);
    }

    public function testServerSideError(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingData = new TrackingQuery(
            'fedex',
            '500 Server Error'
        );

        try {
            $shipengine->trackPackage($trackingData);
        } catch (ShipEngineException $err) {
            $error = $err->jsonSerialize();
            $this->assertInstanceOf(SystemException::class, $err);
            $this->assertNotEmpty($error['requestId']);
            $this->assertStringStartsWith('req_', $error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::SYSTEM, $error['type']);
            $this->assertEquals(ErrorCode::UNSPECIFIED, $error['errorCode']);
            $this->assertEquals(
                "Unable to process this request. A downstream API error occurred.",
                $error['message']
            );
        }
    }

    public function testMultipleExceptionsInTrackingEvents(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_DeLiveredException');

        $this->trackPackageAssertions($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->assertCount(8, $trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
        $this->assertEquals('exception', $trackingResult->events[4]->status);
        $this->assertEquals('exception', $trackingResult->events[5]->status);
    }

    public function testMultipleLocationsInTrackingEvent(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_Attempted');

        $this->trackPackageAssertions($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->assertNull($trackingResult->events[0]->location);
        $this->assertNull($trackingResult->events[4]->location->latitude);
        $this->assertNull($trackingResult->events[4]->location->longitude);
        $this->assertNotNull($trackingResult->events[2]->location->latitude);
        $this->assertNotNull($trackingResult->events[2]->location->longitude);
    }

    public function testCarrierDateTimeWithoutTimezone(): void
    {
        $shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz_sim',
                'baseUrl' => Endpoints::TEST_RPC_URL,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15S')
            )
        );
        $trackingResult = $shipengine->trackPackage('pkg_Attempted');

        $this->trackPackageAssertions($trackingResult);
        $this->assertEventsInOrder($trackingResult->events);
        $this->assertArrayHasKey(0, $trackingResult->events);
        $this->assertArrayHasKey(1, $trackingResult->events);
        $this->assertArrayHasKey(2, $trackingResult->events);

        foreach ($trackingResult->events as $event) {
            $this->assertNotNull($event->dateTime);
            $this->assertNotNull($event->carrierDateTime);
            $this->assertTrue($event->dateTime->hasTimezone());
            $this->assertFalse($event->carrierDateTime->hasTimezone());
        }
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

    public function doesDeliveryDateMatch(TrackPackageResult $trackingResult): void
    {
        $this->assertEquals(
            $trackingResult->shipment->actualDeliveryDate,
            $trackingResult->events[array_key_last($trackingResult->events)]->dateTime
        );
    }

    public function assertEventsInOrder(array $events): void
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
