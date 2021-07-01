<?php declare(strict_types=1);

namespace ShipEngine\Service\Package;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Package\TrackingQuery;
use ShipEngine\Model\Package\TrackPackageResult;
use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Assert;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * Track a given package to obtain status updates on it's progression through the fulfillment cycle.
 *
 * <br>
 * **Usage**:
 * ```php
 * $trackingService = new TrackingService();
 * $trackingService->track(args);
 * ```
 *
 * @package ShipEngine\Service\Package
 */
final class TrackPackageService
{
    /**
     * Track a package by `trackingNumber` and `carrierCode` via the **TrackingQuery** object, or ou can track by
     * passing in a string that is the **packageId** of the shipment you wish to track.
     *
     * <br>
     * **Ways to track**:
     * - By tracking number and carrier code: pass in an instance of **TrackingQuery** which has properties for
     * **trackingNumber** and **carrierCode**.
     * - By package id: pass in a **string** that is the **packageId** of the shipment you wish to track.
     *
     * @param ShipEngineConfig $config ShipEngine configuration object.
     * @return TrackPackageResult
     * @throws ClientExceptionInterface
     */
    public function track(
        ShipEngineConfig $config,
        $trackingData
    ): TrackPackageResult {
        $assert = new Assert();
        $client = new ShipEngineClient();

        if (is_string($trackingData)) {
            $assert->isPackageIdValid($trackingData);

            $apiResponse = $client->request(
                RPCMethods::TRACK_PACKAGE,
                $config,
                array('packageId ' => $trackingData)
            );
            return new TrackPackageResult($apiResponse, $config);
        }

        if ($trackingData instanceof TrackingQuery) {
            $apiResponse = $client->request(
                RPCMethods::TRACK_PACKAGE,
                $config,
                $trackingData->jsonSerialize()
            );
            return new TrackPackageResult($apiResponse, $config);
        }

        throw new ShipEngineException('Could not track package with the arguments provided.');
    }
}
