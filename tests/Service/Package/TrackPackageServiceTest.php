<?php declare(strict_types=1);

namespace Service\Package;

use DateInterval;
use PHPUnit\Framework\TestCase;
use ShipEngine\ShipEngine;
use ShipEngine\Util\Constants\Endpoints;

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

    public function testInitialScanTrackingEvent()
    {
        $trackingResult = self::$shipengine->trackPackage('pkg_1FedExAccepted');

        $this->assertNotNull($trackingResult->shipment->carrierAccount->carrier->code);
        $this->assertNotEmpty($trackingResult->shipment->carrierAccount->carrier->code);
        $this->assertIsString($trackingResult->shipment->carrierAccount->carrier->code);

        $this->assertNotNull($trackingResult->shipment->carrier->code);
        $this->assertNotEmpty($trackingResult->shipment->carrier->code);
        $this->assertIsString($trackingResult->shipment->carrier->code);

        $this->assertNotNull($trackingResult->shipment->estimatedDeliveryDate);
        $this->assertNotEmpty($trackingResult->shipment->estimatedDeliveryDate);
        $this->assertArrayHasKey(0, $trackingResult->events);
        $this->assertArrayNotHasKey(1, $trackingResult->events);
        $this->assertEquals('accepted', $trackingResult->events[0]->status);
    }
}
